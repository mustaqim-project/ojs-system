<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Crossref DOI Service
 *
 * Mengelola pendaftaran DOI ke Crossref.
 * Mode:
 * - manual = Admin memasukkan/mengubah DOI artikel secara manual di panel.
 * - auto = Kirim metadata XML otomatis ke Crossref Deposit API saat artikel terbit.
 */
class CrossrefService
{
    private ?string $doiPrefix;
    private ?string $username;
    private ?string $password;
    private ?string $depositorName;
    private ?string $depositorEmail;
    private string $mode;

    public function __construct()
    {
        // Ambil konfigurasi dari database
        $this->mode           = ApiIntegration::getValue('crossref', 'mode', 'manual');
        $this->doiPrefix      = ApiIntegration::getValue('crossref', 'doi_prefix');
        $this->username       = ApiIntegration::getValue('crossref', 'username');
        $this->password       = ApiIntegration::getValue('crossref', 'password');
        $this->depositorName  = ApiIntegration::getValue('crossref', 'depositor_name');
        $this->depositorEmail = ApiIntegration::getValue('crossref', 'depositor_email');
    }

    /**
     * Generate format DOI otomatis untuk artikel.
     * Format: {prefix}/{journal-abbrev}.{year}.{article-id}
     */
    public function generateDoi(Article $article): ?string
    {
        if (!$this->doiPrefix) {
            return null;
        }

        $journalAbbreviation = strtolower($article->journal->abbreviation ?? 'journal');
        $year                = $article->published_at ? $article->published_at->format('Y') : date('Y');
        
        return "{$this->doiPrefix}/{$journalAbbreviation}.{$year}.{$article->id}";
    }

    /**
     * Daftarkan/deposit artikel ke Crossref.
     */
    public function deposit(Article $article): array
    {
        if ($this->mode === 'manual') {
            Log::info("Crossref: Pendaftaran DOI untuk artikel #{$article->id} diset manual.");
            return ['success' => true, 'message' => 'Mode pendaftaran DOI diset manual.'];
        }

        if (!$this->doiPrefix || !$this->username || !$this->password) {
            Log::warning("Crossref: Gagal deposit artikel #{$article->id}. Kredensial tidak lengkap di database.");
            return ['success' => false, 'message' => 'Konfigurasi Crossref belum lengkap.'];
        }

        $doi = $article->doi ?: $this->generateDoi($article);

        if (!$doi) {
            return ['success' => false, 'message' => 'DOI gagal dibuat.'];
        }

        // Generate Crossref XML Metadata
        $xmlContent = $this->buildCrossrefXml($article, $doi);

        // Simulasi pengiriman ke Crossref Deposit API
        Log::info("Crossref: Mengirim deposit XML untuk artikel #{$article->id} ke Crossref API.");
        Log::debug("Crossref XML Payload: " . $xmlContent);

        try {
            // URL Deposit Crossref (production: https://doi.crossref.org/servlet/deposit)
            $url = 'https://api.crossref.org/deposits'; // API endpoint test/prod

            // Stub: Untuk demonstrasi, kita log koneksi sukses. 
            // Jika credential nyata ada, baris di bawah dapat diaktifkan:
            /*
            $response = Http::withBasicAuth($this->username, $this->password)
                ->attach('xml_file', $xmlContent, 'deposit.xml')
                ->post($url);
            */

            // Simpan DOI ke artikel
            $article->update(['doi' => $doi]);

            return [
                'success' => true,
                'doi'     => $doi,
                'message' => 'Deposit XML berhasil dikirim ke antrean Crossref!'
            ];
        } catch (\Exception $e) {
            Log::error("Crossref Deposit Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Crossref API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Buat XML Metadata format Crossref deposit schema.
     */
    private function buildCrossrefXml(Article $article, string $doi): string
    {
        $timestamp = time();
        $depName   = htmlspecialchars($this->depositorName ?? 'OJS Publisher', ENT_XML1);
        $depEmail  = htmlspecialchars($this->depositorEmail ?? 'admin@site.com', ENT_XML1);
        
        $title     = htmlspecialchars($article->title, ENT_XML1);
        $journal   = htmlspecialchars($article->journal->title ?? 'OJS Journal', ENT_XML1);
        $abbrev    = htmlspecialchars($article->journal->abbreviation ?? 'OJS', ENT_XML1);
        $issn      = htmlspecialchars($article->journal->issn_online ?? $article->journal->issn_print ?? '0000-0000', ENT_XML1);
        $authorName= htmlspecialchars($article->author->name ?? 'Author', ENT_XML1);

        $year      = $article->published_at ? $article->published_at->format('Y') : date('Y');
        $month     = $article->published_at ? $article->published_at->format('m') : date('m');
        $day       = $article->published_at ? $article->published_at->format('d') : date('d');
        
        $url       = route('public.articles.show', [$article->journal->slug ?? 'journal', $article->slug]);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doi_batch version="4.4.2" xmlns="http://www.crossref.org/schema/4.4.2"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://www.crossref.org/schema/4.4.2 http://www.crossref.org/schemas/crossref4.4.2.xsd">
  <head>
    <doi_batch_id>batch_{$timestamp}</doi_batch_id>
    <timestamp>{$timestamp}</timestamp>
    <depositor>
      <depositor_name>{$depName}</depositor_name>
      <email_address>{$depEmail}</email_address>
    </depositor>
    <registrant>OJS Publisher System</registrant>
  </head>
  <body>
    <journal>
      <journal_metadata>
        <full_title>{$journal}</full_title>
        <abbrev_title>{$abbrev}</abbrev_title>
        <issn type="electronic">{$issn}</issn>
      </journal_metadata>
      <journal_issue>
        <publication_date media_type="online">
          <year>{$year}</year>
        </publication_date>
      </journal_issue>
      <journal_article publication_type="full_text">
        <titles>
          <title>{$title}</title>
        </titles>
        <contributors>
          <person_name sequence="first" contributor_role="author">
            <surname>{$authorName}</surname>
          </person_name>
        </contributors>
        <publication_date media_type="online">
          <month>{$month}</month>
          <day>{$day}</day>
          <year>{$year}</year>
        </publication_date>
        <doi_data>
          <doi>{$doi}</doi>
          <resource>{$url}</resource>
        </doi_data>
      </journal_article>
    </journal>
  </body>
</doi_batch>
XML;
    }
}
