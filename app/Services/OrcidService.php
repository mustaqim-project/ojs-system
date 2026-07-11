<?php

namespace App\Services;

use App\Models\ApiIntegration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ORCID API Service
 *
 * SEMUA KONFIGURASI DIBACA DARI DATABASE (tabel api_integrations).
 * Tidak ada dependency ke .env — admin mengkonfigurasi via panel.
 *
 * MODE SAAT INI: Public API (gratis, read-only)
 * - Scope: /read-public (client_credentials flow)
 *
 * UPGRADE KE MEMBER API (tanpa refactor):
 * 1. Admin ubah "Mode API" ke "Member API" di panel Integrasi → ORCID
 * 2. Isi Member API credentials
 * 3. Service ini otomatis menggunakan endpoint & scope yang sesuai
 */
class OrcidService
{
    private const PUBLIC_API_URL  = 'https://pub.orcid.org/v3.0/';
    private const MEMBER_API_URL  = 'https://api.orcid.org/v3.0/';
    private const SANDBOX_PUB_URL = 'https://pub.sandbox.orcid.org/v3.0/';
    private const SANDBOX_MEM_URL = 'https://api.sandbox.orcid.org/v3.0/';

    private Client $http;
    private string $mode;
    private bool $isSandbox;

    public function __construct()
    {
        // Baca config dari database
        $this->mode      = ApiIntegration::getValue('orcid', 'mode', 'public');
        $this->isSandbox = (bool) ApiIntegration::getValue('orcid', 'sandbox', false);

        $baseUrl = match ([$this->mode, $this->isSandbox]) {
            ['member', false] => self::MEMBER_API_URL,
            ['member', true]  => self::SANDBOX_MEM_URL,
            ['public', true]  => self::SANDBOX_PUB_URL,
            default           => self::PUBLIC_API_URL,
        };

        $this->http = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => 10,
            'headers'  => ['Accept' => 'application/json'],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // TOKEN MANAGEMENT
    // ─────────────────────────────────────────────────────────

    /**
     * Public API token via client_credentials.
     * Di-cache 20 jam (token ORCID berlaku 24 jam).
     */
    public function getPublicAccessToken(): ?string
    {
        return Cache::remember('orcid_public_token', 72000, function () {
            $clientId     = ApiIntegration::getValue('orcid', 'client_id');
            $clientSecret = ApiIntegration::getValue('orcid', 'client_secret');

            if (!$clientId || !$clientSecret) {
                Log::warning('ORCID: client_id atau client_secret belum dikonfigurasi di admin panel.');
                return null;
            }

            $tokenUrl = $this->isSandbox
                ? 'https://sandbox.orcid.org/oauth/token'
                : 'https://orcid.org/oauth/token';

            try {
                $client   = new Client(['timeout' => 10]);
                $response = $client->post($tokenUrl, [
                    'form_params' => [
                        'client_id'     => $clientId,
                        'client_secret' => $clientSecret,
                        'grant_type'    => 'client_credentials',
                        'scope'         => '/read-public',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                return $data['access_token'] ?? null;
            } catch (GuzzleException $e) {
                Log::warning('ORCID: Gagal mendapatkan public token', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    // ─────────────────────────────────────────────────────────
    // PUBLIC API — READ
    // ─────────────────────────────────────────────────────────

    public function getRecord(string $orcidId, ?string $userToken = null): ?array
    {
        return Cache::remember("orcid_record_{$orcidId}", 21600, function () use ($orcidId, $userToken) {
            return $this->get("{$orcidId}/record", $userToken);
        });
    }

    public function getPerson(string $orcidId, ?string $userToken = null): ?array
    {
        return $this->get("{$orcidId}/person", $userToken);
    }

    public function getWorks(string $orcidId, ?string $userToken = null): ?array
    {
        return $this->get("{$orcidId}/works", $userToken);
    }

    public function getEmployments(string $orcidId, ?string $userToken = null): ?array
    {
        return $this->get("{$orcidId}/employments", $userToken);
    }

    /**
     * Parse profil ringkas untuk auto-fill form registrasi/profil user.
     */
    public function parseSummaryProfile(string $orcidId, ?string $userToken = null): array
    {
        $person = $this->getPerson($orcidId, $userToken);
        if (!$person) return [];

        $givenName  = $person['name']['given-names']['value']  ?? '';
        $familyName = $person['name']['family-name']['value']  ?? '';
        $name       = trim("{$givenName} {$familyName}") ?: ($person['name']['credit-name']['value'] ?? '');
        $bio        = $person['biography']['content'] ?? null;

        $affiliation = null;
        $emps = $this->getEmployments($orcidId, $userToken);
        if ($emps) {
            $latest = collect($emps['affiliation-group'] ?? [])
                ->sortByDesc(fn($g) => $g['last-modified-date']['value'] ?? 0)
                ->first();
            if ($latest) {
                $affiliation = $latest['summaries'][0]['employment-summary']['organization']['name'] ?? null;
            }
        }

        return array_filter([
            'name'        => $name,
            'bio'         => $bio,
            'affiliation' => $affiliation,
            'orcid_url'   => self::normalizeOrcidId($orcidId),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // MEMBER API — WRITE (aktif saat mode=member)
    // ─────────────────────────────────────────────────────────

    /**
     * Deposit karya ke profil ORCID peneliti.
     * Hanya aktif jika mode = member (dikonfigurasi admin).
     */
    public function addWork(string $orcidId, string $userToken, array $workData): ?string
    {
        if ($this->mode !== 'member') {
            Log::info('ORCID: addWork diabaikan (mode Public API). Ubah ke Member API di Admin → Integrasi → ORCID.');
            return null;
        }

        try {
            $response = $this->http->post("{$orcidId}/work", [
                'headers' => [
                    'Authorization' => "Bearer {$userToken}",
                    'Content-Type'  => 'application/vnd.orcid+xml',
                    'Accept'        => 'application/json',
                ],
                'body' => $this->buildWorkXml($workData),
            ]);
            $location = $response->getHeader('Location')[0] ?? null;
            return $location ? basename($location) : null;
        } catch (GuzzleException $e) {
            Log::error('ORCID: Gagal addWork', ['orcid' => $orcidId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function deleteWork(string $orcidId, string $userToken, string $putCode): bool
    {
        if ($this->mode !== 'member') return false;

        try {
            $this->http->delete("{$orcidId}/work/{$putCode}", [
                'headers' => ['Authorization' => "Bearer {$userToken}"],
            ]);
            return true;
        } catch (GuzzleException $e) {
            Log::error('ORCID: Gagal deleteWork', ['put_code' => $putCode, 'error' => $e->getMessage()]);
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────

    private function get(string $path, ?string $userToken = null): ?array
    {
        try {
            $token = $userToken ?? $this->getPublicAccessToken();
            if (!$token) return null;

            $response = $this->http->get($path, [
                'headers' => ['Authorization' => "Bearer {$token}"],
            ]);
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::warning("ORCID: Gagal fetch {$path}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function buildWorkXml(array $data): string
    {
        $title   = htmlspecialchars($data['title'] ?? '', ENT_XML1);
        $doi     = htmlspecialchars($data['doi'] ?? '', ENT_XML1);
        $year    = $data['year'] ?? date('Y');
        $url     = htmlspecialchars($data['url'] ?? '', ENT_XML1);
        $journal = htmlspecialchars($data['journal'] ?? '', ENT_XML1);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<work:work xmlns:work="http://www.orcid.org/ns/work"
           xmlns:common="http://www.orcid.org/ns/common">
  <work:title><common:title>{$title}</common:title></work:title>
  <work:type>journal-article</work:type>
  <common:publication-date><common:year>{$year}</common:year></common:publication-date>
  <common:external-ids>
    <common:external-id>
      <common:external-id-type>doi</common:external-id-type>
      <common:external-id-value>{$doi}</common:external-id-value>
      <common:external-id-relationship>self</common:external-id-relationship>
    </common:external-id>
  </common:external-ids>
  <work:journal-title>{$journal}</work:journal-title>
  <common:url>{$url}</common:url>
</work:work>
XML;
    }

    public static function isValidOrcidId(string $id): bool
    {
        $clean = preg_replace('#^https?://orcid\.org/#', '', $id);
        return (bool) preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $clean);
    }

    public static function normalizeOrcidId(string $id): string
    {
        $clean = preg_replace('#^https?://orcid\.org/#', '', $id);
        return "https://orcid.org/{$clean}";
    }

    /**
     * Cek apakah ORCID integration sudah dikonfigurasi dan aktif.
     */
    public static function isConfigured(): bool
    {
        return ApiIntegration::isEnabled('orcid')
            && ApiIntegration::getValue('orcid', 'client_id')
            && ApiIntegration::getValue('orcid', 'client_secret');
    }
}
