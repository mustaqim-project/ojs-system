<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\Article;
use App\Models\Journal;
use Illuminate\Support\Facades\Log;

/**
 * OAI-PMH Service
 *
 * Mengimplementasikan protokol OAI-PMH v2.0 untuk memfasilitasi harvesting metadata.
 * Format metadata yang didukung: oai_dc (Dublin Core).
 */
class OaiPmhService
{
    /**
     * Handle request OAI-PMH berdasarkan parameter.
     */
    public function handle(array $params): string
    {
        $verb = $params['verb'] ?? null;

        if (!$verb) {
            return $this->errorResponse('badVerb', 'Verb parameter is missing');
        }

        return match ($verb) {
            'Identify'            => $this->identifyResponse($params),
            'ListMetadataFormats' => $this->listMetadataFormatsResponse($params),
            'ListSets'            => $this->listSetsResponse($params),
            'ListIdentifiers'     => $this->listIdentifiersOrRecordsResponse($params, false),
            'ListRecords'         => $this->listIdentifiersOrRecordsResponse($params, true),
            'GetRecord'           => $this->getRecordResponse($params),
            default               => $this->errorResponse('badVerb', 'Value of the verb parameter is not a legal OAI-PMH verb'),
        };
    }

    // ─────────────────────────────────────────────────────────
    // VERB RESPONSES
    // ─────────────────────────────────────────────────────────

    private function identifyResponse(array $params): string
    {
        $repoName  = ApiIntegration::getValue('oai_pmh', 'repository_name') ?? config('app.name', 'OJS Repositori');
        $adminEmail = ApiIntegration::getValue('oai_pmh', 'admin_email') ?? 'admin@' . request()->getHost();
        $baseUrl   = url('/oai');
        $earliest  = Article::published()->min('published_at')?->format('Y-m-d\TH:i:s\Z') ?? '2020-01-01T00:00:00Z';

        $xml = $this->headerXml('Identify', $params);
        $xml .= <<<XML
  <Identify>
    <repositoryName>{$repoName}</repositoryName>
    <baseURL>{$baseUrl}</baseURL>
    <protocolVersion>2.0</protocolVersion>
    <adminEmail>{$adminEmail}</adminEmail>
    <earliestDatestamp>{$earliest}</earliestDatestamp>
    <deletedRecord>no</deletedRecord>
    <granularity>YYYY-MM-DDThh:mm:ssZ</granularity>
  </Identify>
XML;
        $xml .= $this->footerXml();
        return $xml;
    }

    private function listMetadataFormatsResponse(array $params): string
    {
        // Cek identifier jika dikirim
        $identifier = $params['identifier'] ?? null;
        if ($identifier) {
            $articleId = $this->parseOaiIdentifier($identifier);
            if (!$articleId || !Article::published()->find($articleId)) {
                return $this->errorResponse('idDoesNotExist', 'The value of the identifier argument is unknown');
            }
        }

        $xml = $this->headerXml('ListMetadataFormats', $params);
        $xml .= <<<XML
  <ListMetadataFormats>
    <metadataFormat>
      <metadataPrefix>oai_dc</metadataPrefix>
      <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
      <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
    </metadataFormat>
  </ListMetadataFormats>
XML;
        $xml .= $this->footerXml();
        return $xml;
    }

    private function listSetsResponse(array $params): string
    {
        $journals = Journal::where('is_active', true)->get();

        $xml = $this->headerXml('ListSets', $params);
        $xml .= "  <ListSets>\n";
        foreach ($journals as $j) {
            $spec = 'journal-' . $j->id;
            $name = htmlspecialchars($j->title, ENT_XML1);
            $xml .= "    <set>\n";
            $xml .= "      <setSpec>{$spec}</setSpec>\n";
            $xml .= "      <setName>{$name}</setName>\n";
            $xml .= "    </set>\n";
        }
        $xml .= "  </ListSets>";
        $xml .= $this->footerXml();
        return $xml;
    }

    private function listIdentifiersOrRecordsResponse(array $params, bool $includeMetadata): string
    {
        $prefix = $params['metadataPrefix'] ?? null;
        
        // metadataPrefix wajib jika resumeToken tidak ada
        if (!$prefix && !isset($params['resumptionToken'])) {
            return $this->errorResponse('cannotDisseminateFormat', 'The metadataPrefix parameter is missing');
        }

        if ($prefix && $prefix !== 'oai_dc') {
            return $this->errorResponse('cannotDisseminateFormat', 'The metadata format is not supported');
        }

        // Query artikel
        $query = Article::published()->with(['journal', 'author']);

        // Filter set (per-jurnal)
        if (isset($params['set'])) {
            $setVal = $params['set'];
            if (preg_match('/^journal-(\d+)$/', $setVal, $matches)) {
                $query->where('journal_id', $matches[1]);
            } else {
                return $this->errorResponse('noRecordsMatch', 'The set spec is invalid or has no matches');
            }
        }

        // Filter date range
        if (isset($params['from'])) {
            $query->where('published_at', '>=', $params['from']);
        }
        if (isset($params['until'])) {
            $query->where('published_at', '<=', $params['until']);
        }

        $articles = $query->latest('published_at')->take(100)->get();

        if ($articles->isEmpty()) {
            return $this->errorResponse('noRecordsMatch', 'No records matches the query parameters');
        }

        $verbName = $includeMetadata ? 'ListRecords' : 'ListIdentifiers';
        $xml = $this->headerXml($verbName, $params);
        $xml .= "  <{$verbName}>\n";

        foreach ($articles as $a) {
            $xml .= $this->renderRecordXml($a, $includeMetadata);
        }

        $xml .= "  </{$verbName}>";
        $xml .= $this->footerXml();
        return $xml;
    }

    private function getRecordResponse(array $params): string
    {
        $identifier = $params['identifier'] ?? null;
        $prefix     = $params['metadataPrefix'] ?? null;

        if (!$identifier) {
            return $this->errorResponse('badArgument', 'Identifier argument is missing');
        }
        if (!$prefix) {
            return $this->errorResponse('badArgument', 'metadataPrefix argument is missing');
        }
        if ($prefix !== 'oai_dc') {
            return $this->errorResponse('cannotDisseminateFormat', 'The metadata format is not supported');
        }

        $articleId = $this->parseOaiIdentifier($identifier);
        $article   = $articleId ? Article::published()->with(['journal', 'author'])->find($articleId) : null;

        if (!$article) {
            return $this->errorResponse('idDoesNotExist', 'The value of the identifier argument is unknown');
        }

        $xml = $this->headerXml('GetRecord', $params);
        $xml .= "  <GetRecord>\n";
        $xml .= $this->renderRecordXml($article, true);
        $xml .= "  </GetRecord>";
        $xml .= $this->footerXml();
        return $xml;
    }

    // ─────────────────────────────────────────────────────────
    // RENDERING RECORD XML
    // ─────────────────────────────────────────────────────────

    private function renderRecordXml(Article $a, bool $includeMetadata): string
    {
        $oaiId     = $this->generateOaiIdentifier($a);
        $datestamp = $a->published_at?->format('Y-m-d\TH:i:s\Z') ?? $a->updated_at->format('Y-m-d\TH:i:s\Z');
        $setSpec   = 'journal-' . $a->journal_id;

        $xml = "    <record>\n";
        $xml .= "      <header>\n";
        $xml .= "        <identifier>{$oaiId}</identifier>\n";
        $xml .= "        <datestamp>{$datestamp}</datestamp>\n";
        $xml .= "        <setSpec>{$setSpec}</setSpec>\n";
        $xml .= "      </header>\n";

        if ($includeMetadata) {
            $title       = htmlspecialchars($a->title, ENT_XML1);
            $creator     = htmlspecialchars($a->author->name, ENT_XML1);
            $description = htmlspecialchars($a->abstract, ENT_XML1);
            $publisher   = htmlspecialchars($a->journal->title, ENT_XML1);
            $date        = $a->published_at?->format('Y-m-d') ?? '';
            $language    = htmlspecialchars($a->language ?? 'id', ENT_XML1);
            $identifierUrl = route('public.articles.show', [$a->journal->slug ?? 'jurnal', $a->slug]);
            
            $xml .= "      <metadata>\n";
            $xml .= "        <oai_dc:dc xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\"\n";
            $xml .= "                   xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
            $xml .= "                   xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
            $xml .= "                   xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">\n";
            
            $xml .= "          <dc:title>{$title}</dc:title>\n";
            $xml .= "          <dc:creator>{$creator}</dc:creator>\n";
            
            // Keywords
            foreach ($a->keywords_array as $kw) {
                if ($kw) {
                    $xml .= "          <dc:subject>" . htmlspecialchars($kw, ENT_XML1) . "</dc:subject>\n";
                }
            }
            
            $xml .= "          <dc:description>{$description}</dc:description>\n";
            $xml .= "          <dc:publisher>{$publisher}</dc:publisher>\n";
            $xml .= "          <dc:date>{$date}</dc:date>\n";
            $xml .= "          <dc:type>info:eu-repo/semantics/article</dc:type>\n";
            $xml .= "          <dc:type>Text</dc:type>\n";
            $xml .= "          <dc:format>application/pdf</dc:format>\n";
            $xml .= "          <dc:identifier>{$identifierUrl}</dc:identifier>\n";
            
            if ($a->doi) {
                $xml .= "          <dc:identifier>doi:{$a->doi}</dc:identifier>\n";
            }
            
            $xml .= "          <dc:language>{$language}</dc:language>\n";
            $xml .= "        </oai_dc:dc>\n";
            $xml .= "      </metadata>\n";
        }

        $xml .= "    </record>\n";
        return $xml;
    }

    // ─────────────────────────────────────────────────────────
    // XML HEADER/FOOTER & ERROR
    // ─────────────────────────────────────────────────────────

    private function headerXml(string $verb, array $params): string
    {
        $responseDate = now()->format('Y-m-d\TH:i:s\Z');
        $requestUrl   = url('/oai');

        $reqAttrs = '';
        foreach ($params as $k => $v) {
            $reqAttrs .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
  <responseDate>{$responseDate}</responseDate>
  <request{$reqAttrs}>{$requestUrl}</request>
XML;
    }

    private function footerXml(): string
    {
        return "\n</OAI-PMH>";
    }

    private function errorResponse(string $code, string $message): string
    {
        $responseDate = now()->format('Y-m-d\TH:i:s\Z');
        $requestUrl   = url('/oai');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
  <responseDate>{$responseDate}</responseDate>
  <request>{$requestUrl}</request>
  <error code="{$code}">{$message}</error>
</OAI-PMH>
XML;
    }

    // ─────────────────────────────────────────────────────────
    // OAI IDENTIFIER PARSING & GENERATION
    // ─────────────────────────────────────────────────────────

    private function generateOaiIdentifier(Article $a): string
    {
        $host = request()->getHost();
        return "oai:{$host}:article/{$a->id}";
    }

    private function parseOaiIdentifier(string $identifier): ?int
    {
        // Format: oai:domain:article/{id}
        $host = request()->getHost();
        $pattern = '/^oai:' . preg_quote($host, '/') . ':article\/(\d+)$/';
        
        if (preg_match($pattern, $identifier, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
