<?php

namespace Database\Seeders;

use App\Models\ApiIntegration;
use Illuminate\Database\Seeder;

/**
 * ApiIntegrationSeeder
 *
 * Mengisi tabel api_integrations dengan definisi field untuk setiap provider.
 * Nilai (credentials) dikosongkan - admin mengisinya via panel.
 * Status default: inactive (harus diaktifkan admin setelah credentials diisi).
 */
class ApiIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        $integrations = [

            // ─────────────────────────────────────────────────
            // ORCID — Researcher Identity & Profile
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'orcid',
                'key'          => 'mode',
                'label'        => 'Mode API',
                'description'  => 'public = gratis (read-only). member = berbayar (read + write ke profil ORCID).',
                'value'        => 'public',
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'select',
                'field_options' => ['public' => 'Public API (Gratis)', 'member' => 'Member API (Berbayar)'],
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'orcid',
                'key'          => 'sandbox',
                'label'        => 'Mode Sandbox (Testing)',
                'description'  => 'Aktifkan saat testing. Gunakan credentials dari sandbox.orcid.org.',
                'value'        => '0',
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'boolean',
                'sort_order'   => 2,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'orcid',
                'key'          => 'client_id',
                'label'        => 'Client ID',
                'description'  => 'Diperoleh dari https://orcid.org/developer-tools (format: APP-XXXXXXXXXXXXXXXX)',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 3,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'orcid',
                'key'          => 'client_secret',
                'label'        => 'Client Secret',
                'description'  => 'Disimpan terenkripsi. Jangan bagikan ke siapapun.',
                'value'        => null,
                'is_secret'    => true,
                'is_required'  => true,
                'field_type'   => 'password',
                'sort_order'   => 4,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'orcid',
                'key'          => 'redirect_uri',
                'label'        => 'Redirect URI (Callback URL)',
                'description'  => 'URL ini harus didaftarkan di aplikasi ORCID Anda. Format: https://domain.com/auth/orcid/callback',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'url',
                'sort_order'   => 5,
                'status'       => 'inactive',
            ],

            // ─────────────────────────────────────────────────
            // GOOGLE — SSO Login
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'google',
                'key'          => 'client_id',
                'label'        => 'Google Client ID',
                'description'  => 'Diperoleh dari Google Cloud Console.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'google',
                'key'          => 'client_secret',
                'label'        => 'Google Client Secret',
                'description'  => 'Disimpan terenkripsi.',
                'value'        => null,
                'is_secret'    => true,
                'is_required'  => true,
                'field_type'   => 'password',
                'sort_order'   => 2,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'google',
                'key'          => 'redirect_uri',
                'label'        => 'Redirect URI (Callback URL)',
                'description'  => 'URL redirect OAuth (contoh: https://domain.com/auth/google/callback)',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'url',
                'sort_order'   => 3,
                'status'       => 'inactive',
            ],

            // ─────────────────────────────────────────────────
            // CROSSREF — DOI Registration
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'crossref',
                'key'          => 'mode',
                'label'        => 'Mode DOI',
                'description'  => 'manual = admin input DOI langsung. auto = deposit otomatis ke Crossref saat artikel dipublish.',
                'value'        => 'manual',
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'select',
                'field_options' => ['manual' => 'Manual (Input DOI sendiri)', 'auto' => 'Otomatis via Crossref API'],
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'crossref',
                'key'          => 'doi_prefix',
                'label'        => 'DOI Prefix',
                'description'  => 'Diperoleh setelah mendaftar ke Crossref. Contoh: 10.12345',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 2,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'crossref',
                'key'          => 'username',
                'label'        => 'Crossref Username',
                'description'  => 'Username akun Crossref member Anda.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 3,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'crossref',
                'key'          => 'password',
                'label'        => 'Crossref Password',
                'description'  => 'Disimpan terenkripsi.',
                'value'        => null,
                'is_secret'    => true,
                'is_required'  => false,
                'field_type'   => 'password',
                'sort_order'   => 4,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'crossref',
                'key'          => 'depositor_name',
                'label'        => 'Depositor Name',
                'description'  => 'Nama institusi untuk deposit metadata ke Crossref.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 5,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'crossref',
                'key'          => 'depositor_email',
                'label'        => 'Depositor Email',
                'description'  => 'Email yang akan diterima notifikasi dari Crossref.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 6,
                'status'       => 'inactive',
            ],

            // ─────────────────────────────────────────────────
            // OAI-PMH — Metadata Harvesting Protocol
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'oai_pmh',
                'key'          => 'enabled',
                'label'        => 'Aktifkan OAI-PMH',
                'description'  => 'Mengaktifkan endpoint /oai untuk indexing oleh Google Scholar, DOAJ, BASE, dll.',
                'value'        => '1',
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'boolean',
                'sort_order'   => 1,
                'status'       => 'active',
            ],
            [
                'provider'     => 'oai_pmh',
                'key'          => 'repository_name',
                'label'        => 'Repository Name',
                'description'  => 'Nama repositori yang ditampilkan di response OAI Identify.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 2,
                'status'       => 'active',
            ],
            [
                'provider'     => 'oai_pmh',
                'key'          => 'admin_email',
                'label'        => 'Admin Email (OAI)',
                'description'  => 'Email admin yang ditampilkan di OAI-PMH Identify response.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 3,
                'status'       => 'active',
            ],
            [
                'provider'     => 'oai_pmh',
                'key'          => 'metadata_formats',
                'label'        => 'Format Metadata',
                'description'  => 'Format yang didukung. oai_dc selalu tersedia (standar wajib OAI-PMH).',
                'value'        => 'oai_dc',
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 4,
                'status'       => 'active',
            ],

            // ─────────────────────────────────────────────────
            // DOAJ — Directory of Open Access Journals
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'doaj',
                'key'          => 'api_key',
                'label'        => 'DOAJ API Key',
                'description'  => 'Diperoleh setelah jurnal diterima di DOAJ. https://doaj.org/account/api',
                'value'        => null,
                'is_secret'    => true,
                'is_required'  => true,
                'field_type'   => 'password',
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'doaj',
                'key'          => 'journal_issn',
                'label'        => 'ISSN Jurnal di DOAJ',
                'description'  => 'ISSN (print atau online) yang terdaftar di DOAJ.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 2,
                'status'       => 'inactive',
            ],

            // ─────────────────────────────────────────────────
            // GOOGLE SCHOLAR — Indexing
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'google_scholar',
                'key'          => 'verification_meta',
                'label'        => 'Google Scholar Verification Meta Tag',
                'description'  => 'Meta tag verifikasi dari Google Search Console (konten atribut "content" saja).',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => false,
                'field_type'   => 'text',
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],

            // ─────────────────────────────────────────────────
            // EMAIL / SMTP — Notifikasi Sistem
            // ─────────────────────────────────────────────────
            [
                'provider'     => 'smtp',
                'key'          => 'host',
                'label'        => 'SMTP Host',
                'description'  => 'Server SMTP untuk pengiriman email. Contoh: smtp.gmail.com',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 1,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'port',
                'label'        => 'SMTP Port',
                'description'  => '587 untuk TLS, 465 untuk SSL, 25 untuk non-secure.',
                'value'        => '587',
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 2,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'username',
                'label'        => 'SMTP Username',
                'description'  => 'Biasanya alamat email pengirim.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 3,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'password',
                'label'        => 'SMTP Password',
                'description'  => 'Disimpan terenkripsi. Untuk Gmail gunakan App Password.',
                'value'        => null,
                'is_secret'    => true,
                'is_required'  => true,
                'field_type'   => 'password',
                'sort_order'   => 4,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'from_address',
                'label'        => 'Alamat Email Pengirim',
                'description'  => 'Alamat email yang terlihat oleh penerima.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 5,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'from_name',
                'label'        => 'Nama Pengirim',
                'description'  => 'Nama yang terlihat oleh penerima email.',
                'value'        => null,
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'text',
                'sort_order'   => 6,
                'status'       => 'inactive',
            ],
            [
                'provider'     => 'smtp',
                'key'          => 'encryption',
                'label'        => 'Enkripsi',
                'description'  => 'Protokol enkripsi koneksi SMTP.',
                'value'        => 'tls',
                'is_secret'    => false,
                'is_required'  => true,
                'field_type'   => 'select',
                'field_options' => ['tls' => 'TLS (direkomendasikan)', 'ssl' => 'SSL', '' => 'Tidak ada'],
                'sort_order'   => 7,
                'status'       => 'inactive',
            ],
        ];

        foreach ($integrations as $data) {
            ApiIntegration::updateOrCreate(
                ['provider' => $data['provider'], 'key' => $data['key']],
                $data
            );
        }
    }
}
