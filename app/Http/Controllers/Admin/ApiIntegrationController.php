<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class ApiIntegrationController extends Controller
{
    /**
     * Halaman utama: daftar semua provider yang tersedia.
     */
    public function index(): View
    {
        // Group by provider, ambil 1 record per provider untuk status overview
        $providers = ApiIntegration::orderBy('provider')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('provider');

        // Meta info untuk setiap provider
        $providerMeta = $this->providerMeta();

        return view('admin.integrations.index', compact('providers', 'providerMeta'));
    }

    /**
     * Halaman konfigurasi satu provider (semua field-nya).
     */
    public function show(string $provider): View
    {
        $fields = ApiIntegration::forProvider($provider)
            ->ordered()
            ->get();

        if ($fields->isEmpty()) {
            abort(404, "Provider '{$provider}' tidak ditemukan.");
        }

        $providerMeta = $this->providerMeta()[$provider] ?? [
            'label'       => ucfirst(str_replace('_', ' ', $provider)),
            'icon'        => 'bi-plug',
            'description' => '',
            'docs_url'    => null,
        ];

        return view('admin.integrations.show', compact('fields', 'provider', 'providerMeta'));
    }

    /**
     * Simpan konfigurasi satu provider.
     * Hanya memproses field yang dikirim — field yang dikosongkan tidak diubah
     * kecuali user centang checkbox "Hapus credential ini".
     */
    public function update(Request $request, string $provider): RedirectResponse
    {
        $fields = ApiIntegration::forProvider($provider)->get()->keyBy('key');

        foreach ($fields as $key => $field) {
            // Jika field password tidak diisi, skip (biarkan nilai lama)
            if ($field->is_secret && !$request->filled("fields.{$key}")) {
                continue;
            }

            // Jika ada perintah hapus credential
            if ($request->boolean("clear.{$key}")) {
                $rawValue = null;
            } else {
                $rawValue = $request->input("fields.{$key}");
            }

            $field->update(['value' => $rawValue]);
        }

        // Update status provider
        if ($request->has('status')) {
            ApiIntegration::where('provider', $provider)
                ->update(['status' => $request->input('status')]);
        }

        // Invalidate semua cache provider ini
        ApiIntegration::clearCache($provider);

        return redirect()
            ->route('admin.integrations.show', $provider)
            ->with('success', 'Konfigurasi ' . $this->providerMeta()[$provider]['label'] . ' berhasil disimpan!');
    }

    /**
     * Test koneksi ke API provider.
     * Mengembalikan JSON status test.
     */
    public function test(Request $request, string $provider): \Illuminate\Http\JsonResponse
    {
        try {
            $result = match ($provider) {
                'orcid'       => $this->testOrcid(),
                'crossref'    => $this->testCrossref(),
                'smtp'        => $this->testSmtp(),
                'oai_pmh'     => $this->testOaiPmh(),
                'doaj'        => $this->testDoaj(),
                default       => ['success' => false, 'message' => 'Test tidak tersedia untuk provider ini.'],
            };
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────
    // TEST METHODS
    // ─────────────────────────────────────────────────────────

    private function testOrcid(): array
    {
        if (!ApiIntegration::getValue('orcid', 'client_id')) {
            return ['success' => false, 'message' => 'Client ID belum diisi.'];
        }
        if (!ApiIntegration::getValue('orcid', 'client_secret')) {
            return ['success' => false, 'message' => 'Client Secret belum diisi.'];
        }

        $orcidService = app(\App\Services\OrcidService::class);
        $token = $orcidService->getPublicAccessToken();

        if ($token) {
            return ['success' => true, 'message' => 'Koneksi ORCID berhasil! Token public diperoleh.'];
        }

        return ['success' => false, 'message' => 'Gagal mendapatkan token dari ORCID. Periksa Client ID & Secret.'];
    }

    private function testCrossref(): array
    {
        $mode = ApiIntegration::getValue('crossref', 'mode', 'manual');

        if ($mode === 'manual') {
            return ['success' => true, 'message' => 'Mode DOI manual aktif. Tidak ada koneksi API yang ditest.'];
        }

        // Test ping ke Crossref API
        try {
            $http = new \GuzzleHttp\Client(['timeout' => 5]);
            $http->get('https://api.crossref.org/works?rows=1');
            return ['success' => true, 'message' => 'Crossref API dapat dijangkau.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Crossref API tidak dapat dijangkau: ' . $e->getMessage()];
        }
    }

    private function testSmtp(): array
    {
        $host = ApiIntegration::getValue('smtp', 'host');
        if (!$host) {
            return ['success' => false, 'message' => 'SMTP Host belum diisi.'];
        }

        // Test koneksi TCP ke SMTP server
        $port       = (int) ApiIntegration::getValue('smtp', 'port', 587);
        $connection = @fsockopen($host, $port, $errno, $errstr, 5);

        if ($connection) {
            fclose($connection);
            return ['success' => true, 'message' => "Koneksi ke {$host}:{$port} berhasil!"];
        }

        return ['success' => false, 'message' => "Gagal koneksi ke {$host}:{$port}. {$errstr}"];
    }

    private function testOaiPmh(): array
    {
        $url = url('/oai?verb=Identify');
        return ['success' => true, 'message' => "OAI-PMH endpoint: {$url}"];
    }

    private function testDoaj(): array
    {
        $apiKey = ApiIntegration::getValue('doaj', 'api_key');
        if (!$apiKey) {
            return ['success' => false, 'message' => 'API Key DOAJ belum diisi.'];
        }

        try {
            $http     = new \GuzzleHttp\Client(['timeout' => 8]);
            $response = $http->get('https://doaj.org/api/search/articles/test', [
                'headers' => ['Authorization' => "Bearer {$apiKey}"],
            ]);
            return ['success' => true, 'message' => 'Koneksi DOAJ berhasil!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'DOAJ error: ' . $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // PROVIDER META — Info tampilan untuk admin panel
    // ─────────────────────────────────────────────────────────

    private function providerMeta(): array
    {
        return [
            'orcid' => [
                'label'       => 'ORCID',
                'icon'        => 'bi-person-badge',
                'color'       => '#a6ce39',
                'description' => 'Integrasi identitas peneliti & auto-fill profil dari ORCID record publik.',
                'docs_url'    => 'https://info.orcid.org/documentation/integration-guide/',
                'badge'       => 'Researcher ID',
            ],
            'crossref' => [
                'label'       => 'Crossref DOI',
                'icon'        => 'bi-link-45deg',
                'color'       => '#e87631',
                'description' => 'Registrasi DOI untuk artikel yang dipublikasikan. Mode manual atau otomatis.',
                'docs_url'    => 'https://www.crossref.org/documentation/register-maintain-records/',
                'badge'       => 'DOI',
            ],
            'oai_pmh' => [
                'label'       => 'OAI-PMH',
                'icon'        => 'bi-broadcast',
                'color'       => '#2563eb',
                'description' => 'Protokol harvesting metadata untuk indexing oleh Google Scholar, DOAJ, BASE.',
                'docs_url'    => 'https://www.openarchives.org/pmh/',
                'badge'       => 'Indexing',
            ],
            'doaj' => [
                'label'       => 'DOAJ',
                'icon'        => 'bi-journals',
                'color'       => '#00b4d8',
                'description' => 'Directory of Open Access Journals — submit metadata artikel ke DOAJ.',
                'docs_url'    => 'https://doaj.org/api/v3/docs',
                'badge'       => 'Open Access',
            ],
            'google_scholar' => [
                'label'       => 'Google Scholar',
                'icon'        => 'bi-mortarboard',
                'color'       => '#4285f4',
                'description' => 'Verifikasi domain untuk Google Scholar Indexing & Search Console.',
                'docs_url'    => 'https://scholar.google.com/intl/en/scholar/inclusion.html',
                'badge'       => 'Indexing',
            ],
            'smtp' => [
                'label'       => 'Email (SMTP)',
                'icon'        => 'bi-envelope',
                'color'       => '#7c3aed',
                'description' => 'Konfigurasi SMTP untuk pengiriman email notifikasi sistem.',
                'docs_url'    => null,
                'badge'       => 'Email',
            ],
            'google' => [
                'label'       => 'Google SSO',
                'icon'        => 'bi-google',
                'color'       => '#ea4335',
                'description' => 'Konfigurasi OAuth 2.0 untuk Login dengan akun Google.',
                'docs_url'    => 'https://console.cloud.google.com/',
                'badge'       => 'Authentication',
            ],
        ];
    }
}
