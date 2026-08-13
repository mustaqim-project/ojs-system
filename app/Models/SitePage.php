<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SitePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'body',
        'meta_description',
        'extra',
        'is_active',
    ];

    protected $casts = [
        'extra'     => 'array',
        'is_active' => 'boolean',
    ];

    // ===========================================================
    // STATIC HELPERS
    // ===========================================================

    /**
     * Cari halaman berdasarkan slug (dengan cache 1 jam).
     * Return null jika tidak ada atau tidak aktif.
     */
    public static function findBySlug(string $slug): ?self
    {
        $page = Cache::get("site_page_{$slug}");

        if ($page instanceof \__PHP_Incomplete_Class || ($page !== null && !$page instanceof self)) {
            Cache::forget("site_page_{$slug}");
            $page = null;
        }

        if ($page === null) {
            $page = static::where('slug', $slug)->where('is_active', true)->first();
            if ($page) {
                Cache::put("site_page_{$slug}", $page, 3600);
            }
        }

        return $page;
    }

    /**
     * Hapus cache saat model disimpan / dihapus.
     */
    protected static function booted(): void
    {
        static::saved(fn($m) => Cache::forget("site_page_{$m->slug}"));
        static::deleted(fn($m) => Cache::forget("site_page_{$m->slug}"));
    }

    // ===========================================================
    // DEFAULT CONTENT — fallback jika belum diisi di DB
    // ===========================================================

    public static function defaults(): array
    {
        return [
            'about' => [
                'title'            => 'Tentang Jurnal',
                'meta_description' => 'Pelajari lebih lanjut tentang jurnal kami, visi, misi, dan sejarah penerbitannya.',
                'body'             => '<h2>Tentang Kami</h2>
<p>Jurnal ini merupakan publikasi ilmiah peer-reviewed dengan akses terbuka (open-access) yang didedikasikan untuk memajukan pengetahuan di berbagai disiplin ilmu. Sejak didirikan, kami berkomitmen untuk menerbitkan penelitian berkualitas tinggi yang memberikan kontribusi nyata bagi komunitas akademik global.</p>
<h3>Visi & Misi</h3>
<p>Visi kami adalah menjadi platform akses terbuka utama untuk penelitian inovatif, membina ekosistem inklusif di mana pengetahuan dibagikan tanpa batas.</p>
<p>Misi kami adalah mempromosikan penyebaran pengetahuan ilmiah melalui proses peninjauan sejawat (peer-review) yang ketat dan penerbitan akses terbuka, membuat hasil penelitian tersedia secara bebas bagi para akademisi dan masyarakat luas di seluruh dunia.</p>',
                'extra' => [
                    'founded_year'     => '2010',
                    'issn_print'       => '0000-0000',
                    'issn_online'      => '0000-0001',
                    'publisher'        => 'OJS Publishing',
                    'frequency'        => 'Kuartalan',
                ],
            ],

            'editorial-team' => [
                'title'            => 'Tim Redaksi',
                'meta_description' => 'Temui dewan redaksi dan tim di balik jurnal ini.',
                'body'             => '<p>Tim redaksi kami terdiri dari para akademisi dan praktisi terkemuka dari seluruh dunia, yang berkomitmen untuk menjaga standar tertinggi peninjauan sejawat akademis.</p>',
                'extra' => [
                    'editor_in_chief' => ['name' => 'Prof. Dr. John Smith', 'affiliation' => 'Universitas Ilmu Pengetahuan', 'email' => 'editor@jurnal.com', 'orcid' => ''],
                    // 'section_editors'  => [
                    //     ['name' => 'Dr. Jane Doe', 'affiliation' => 'MIT, AS', 'area' => 'Ilmu Komputer'],
                    //     ['name' => 'Dr. Ahmad Fauzi', 'affiliation' => 'UI, Indonesia', 'area' => 'Sistem Informasi'],
                    //     ['name' => 'Dr. Maria Santos', 'affiliation' => 'USP, Brasil', 'area' => 'Rekayasa Data'],
                    // ],
                ],
            ],

            'reviewer-board' => [
                'title'            => 'Dewan Penelaah',
                'meta_description' => 'Dewan penelaah ahli kami yang memastikan kualitas peninjauan sejawat.',
                'body'             => '<p>Kami sangat berterima kasih kepada anggota dewan penelaah kami yang berdedikasi yang secara sukarela menyumbangkan keahlian mereka untuk menjaga kualitas penelitian yang diterbitkan.</p>',
                'extra' => [
                    'reviewers' => [
                        ['name' => 'Dr. Reviewer Satu', 'affiliation' => 'Harvard University, AS', 'area' => 'Pembelajaran Mesin'],
                        ['name' => 'Dr. Reviewer Dua', 'affiliation' => 'Oxford University, Inggris', 'area' => 'Algoritma'],
                        ['name' => 'Dr. Reviewer Tiga', 'affiliation' => 'ETH Zurich, Swiss', 'area' => 'Sistem'],
                        ['name' => 'Dr. Reviewer Empat', 'affiliation' => 'NUS, Singapura', 'area' => 'Basis Data'],
                        ['name' => 'Dr. Reviewer Lima', 'affiliation' => 'IIT Delhi, India', 'area' => 'Jaringan'],
                        ['name' => 'Dr. Reviewer Enam', 'affiliation' => 'UGM, Indonesia', 'area' => 'Rekayasa Perangkat Lunak'],
                    ],
                ],
            ],

            'author-guidelines' => [
                'title'            => 'Panduan Penulis',
                'meta_description' => 'Semua yang perlu Anda ketahui untuk mempersiapkan dan mengirimkan naskah Anda.',
                'body'             => '<h3>Daftar Periksa Penyerahan</h3>
<p>Sebelum mengirimkan naskah Anda, pastikan naskah tersebut memenuhi semua persyaratan berikut:</p>
<ul>
  <li>Kiriman tersebut belum pernah dipublikasikan sebelumnya, dan juga tidak sedang dipertimbangkan oleh jurnal lain.</li>
  <li>File naskah dalam format dokumen OpenOffice, Microsoft Word, atau RTF.</li>
  <li>Bila tersedia, URL dan DOI untuk referensi telah disertakan.</li>
  <li>Teks tersebut mematuhi persyaratan gaya dan bibliografi yang diuraikan dalam pedoman ini.</li>
  <li>Surat pengantar disertakan, ditujukan kepada editor dan merinci kebaruan penelitian.</li>
</ul>
<h3>Pemformatan Naskah</h3>
<p>Naskah harus ditulis dalam bahasa Inggris (atau Indonesia) yang jelas dan ringkas. Struktur artikel biasanya harus mengikuti format IMRaD (Pendahuluan, Metode, Hasil, dan Pembahasan).</p>
<h5>Halaman Judul</h5>
<p>Halaman judul harus mencantumkan judul artikel, nama lengkap penulis, afiliasi, dan alamat email penulis korespondensi. Abstrak 150-250 kata dan 3-5 kata kunci juga harus disertakan.</p>
<h5>Gambar & Tabel</h5>
<p>Semua gambar dan tabel harus dikutip dalam teks dan diberi nomor secara berurutan. Gambar beresolusi tinggi (minimal 300 DPI) harus diunggah sebagai file terpisah selama pengiriman jika diperlukan.</p>
<h3>Gaya Referensi</h3>
<p>Kami menggunakan gaya <strong>APA (American Psychological Association) Edisi ke-7</strong> untuk kutipan dan referensi. Penulis sangat didorong untuk menggunakan perangkat lunak manajemen referensi seperti Mendeley, Zotero, atau EndNote.</p>
<div style="background: var(--bg-app); padding: 20px; border-radius: 8px; margin-top: 20px;">
    <h6 style="font-weight: 700; margin-bottom: 12px; font-size: 14px;">Contoh Kutipan Artikel Jurnal:</h6>
    <p style="font-size: 14px; color: var(--text-muted); font-family: monospace; margin: 0;">Grady, J. S., Her, M., Moreno, G., Perez, C., & Yelinek, J. (2019). Emotions in storybooks: A comparison of storybooks that represent ethnic and racial groups in the United States. Psychology of Popular Media Culture, 8(3), 207–217. https://doi.org/10.1037/ppm0000185</p>
</div>',
                'extra' => [
                    'template_url' => '',
                    'apc_waiver'   => 'Keringanan biaya tersedia bagi penulis dari negara berpenghasilan rendah.',
                ],
            ],

            'ethics' => [
                'title'            => 'Etika Publikasi',
                'meta_description' => 'Komitmen kami terhadap praktik penerbitan yang etis.',
                'body'             => '<h3>Standar Editorial</h3>
<p>Jurnal ini mengikuti pedoman <a href="https://publicationethics.org/" target="_blank">COPE (Committee on Publication Ethics)</a> untuk praktik publikasi yang bertanggung jawab.</p>
<h3>Kepenulisan</h3>
<p>Semua penulis yang terdaftar harus memberikan kontribusi signifikan terhadap penelitian. Kepenulisan tamu (guest) atau siluman (ghost) tidak diizinkan.</p>
<h3>Plagiarisme</h3>
<p>Semua naskah disaring dari plagiarisme menggunakan perangkat lunak otomatis. Plagiarisme dalam bentuk apa pun dilarang keras.</p>
<h3>Konflik Kepentingan</h3>
<p>Penulis harus mengungkapkan hubungan keuangan atau pribadi yang dapat dianggap memengaruhi hasil kerja mereka.</p>',
                'extra' => [],
            ],

            'peer-review' => [
                'title'            => 'Proses Tinjauan Sejawat',
                'meta_description' => 'Proses peninjauan sejawat yang transparan dan double-blind.',
                'body'             => '<h3>Double-Blind Review</h3>
<p>Jurnal ini menggunakan peninjauan sejawat double-blind, di mana penulis dan penelaah tetap anonim selama proses peninjauan.</p>
<h3>Lini Masa Peninjauan</h3>
<ul>
  <li><strong>Pemeriksaan awal editor:</strong> 1–2 minggu</li>
  <li><strong>Peninjauan sejawat:</strong> 4–8 minggu</li>
  <li><strong>Revisi &amp; keputusan:</strong> 2–4 minggu</li>
  <li><strong>Publikasi setelah diterima:</strong> 2–4 minggu</li>
</ul>
<h3>Kriteria Penilaian</h3>
<p>Penelaah mengevaluasi naskah berdasarkan orisinalitas, metodologi, signifikansi hasil, kejelasan penulisan, dan kepatuhan terhadap standar etika.</p>',
                'extra' => [],
            ],

            'focus-and-scope' => [
                'title'            => 'Fokus dan Ruang Lingkup',
                'meta_description' => 'Ruang lingkup dan fokus area dari jurnal kami.',
                'body'             => '<h3>Ruang Lingkup</h3>
<p>Jurnal ini menerbitkan penelitian peer-reviewed di berbagai disiplin ilmu, dengan fokus pada karya interdisipliner yang menjembatani teori dan praktik.</p>
<h3>Topik yang Diminati</h3>
<ul>
  <li>Ilmu Komputer &amp; Kecerdasan Buatan</li>
  <li>Sistem Informasi &amp; Teknologi</li>
  <li>Sains Data &amp; Analisis</li>
  <li>Rekayasa Perangkat Lunak &amp; Pengembangan</li>
  <li>Keamanan Siber &amp; Privasi</li>
  <li>Interaksi Manusia-Komputer</li>
</ul>',
                'extra' => [],
            ],

            'journal-policies' => [
                'title'            => 'Kebijakan Jurnal',
                'meta_description' => 'Kebijakan akses dan kebijakan pengarsipan.',
                'body'             => '<h3>Kebijakan Akses Terbuka</h3>
<p>Jurnal ini merupakan jurnal akses terbuka (open-access) yang berarti semua konten tersedia secara bebas tanpa biaya bagi pengguna atau lembaga mereka. Pengguna diizinkan untuk membaca, mengunduh, menyalin, mendistribusikan, mencetak, mencari, atau menautkan ke teks lengkap artikel.</p>
<h3>Kebijakan Pengarsipan Mandiri</h3>
<p>Penulis dapat mengarsipkan naskah yang diterima (post-print) di repositori institusional mereka segera setelah naskah diterima.</p>
<h3>Biaya Pemrosesan Artikel (APC)</h3>
<p>Jurnal ini mengenakan Biaya Pemrosesan Artikel (APC) setelah naskah dinyatakan diterima. Keringanan biaya tersedia. Lihat kebijakan APC kami untuk detailnya.</p>',
                'extra' => [],
            ],

            'indexing' => [
                'title'            => 'Pengindeksan & Abstrak',
                'meta_description' => 'Basis data dan indeks yang mencakup jurnal kami.',
                'body'             => '<p>Jurnal ini terindeks dan terabstrak dalam basis data berikut:</p>',
                'extra' => [
                    'indexes' => [
                        ['name' => 'DOAJ', 'url' => 'https://doaj.org', 'logo' => ''],
                        ['name' => 'Scopus', 'url' => 'https://scopus.com', 'logo' => ''],
                        ['name' => 'Google Scholar', 'url' => 'https://scholar.google.com', 'logo' => ''],
                        ['name' => 'SINTA', 'url' => 'https://sinta.kemdikbud.go.id', 'logo' => ''],
                        ['name' => 'Garuda', 'url' => 'https://garuda.kemdikbud.go.id', 'logo' => ''],
                        ['name' => 'Crossref', 'url' => 'https://crossref.org', 'logo' => ''],
                    ],
                ],
            ],

            'contact' => [
                'title'            => 'Hubungi Kami',
                'meta_description' => 'Hubungi tim editorial kami.',
                'body'             => '<p>Kami menyambut baik pertanyaan mengenai pengiriman naskah, proses peninjauan sejawat, dan masalah jurnal umum lainnya.</p>',
                'extra' => [
                    'email'   => 'editor@jurnal.com',
                    'phone'   => '+62 21 1234 5678',
                    'address' => 'Jl. Contoh No. 1, Jakarta 12345, Indonesia',
                    'maps_embed_url' => '',
                ],
            ],

            'privacy-policy' => [
                'title'            => 'Kebijakan Privasi',
                'meta_description' => 'Bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.',
                'body'             => '<h3>1. Pendahuluan</h3>
<p>Nama dan alamat email yang dimasukkan dalam situs jurnal ini akan digunakan secara eksklusif untuk tujuan jurnal ini dan tidak akan tersedia untuk tujuan lain atau pihak lain.</p>
<h3>2. Data yang Kami Kumpulkan</h3>
<p>Kami mengumpulkan informasi yang Anda berikan selama pendaftaran, pengiriman, dan proses peninjauan, termasuk nama, alamat email, afiliasi, dan konten naskah Anda.</p>
<h3>3. Bagaimana Kami Menggunakan Data Anda</h3>
<p>Data Anda digunakan semata-mata untuk mengelola alur kerja editorial, berkomunikasi dengan Anda tentang kiriman Anda, dan meningkatkan layanan kami.</p>',
                'extra' => [],
            ],

            'terms-conditions' => [
                'title'            => 'Syarat & Ketentuan',
                'meta_description' => 'Syarat dan ketentuan untuk menggunakan platform ini.',
                'body'             => '<h3>1. Penerimaan Syarat</h3>
<p>Dengan mendaftar, mengakses, atau menggunakan platform penerbitan ilmiah ini, Anda setuju untuk mematuhi dan terikat oleh Syarat dan Ketentuan ini.</p>
<h3>2. Tanggung Jawab Pengguna</h3>
<p>Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun Anda dan untuk semua aktivitas yang terjadi di bawah akun Anda.</p>
<h3>3. Kekayaan Intelektual</h3>
<p>Artikel yang diterbitkan dilindungi di bawah lisensi Creative Commons sebagaimana ditentukan dalam setiap artikel. Desain platform dan perangkat lunak tetap menjadi milik penerbit.</p>',
                'extra' => [],
            ],

            'announcements' => [
                'title'            => 'Pengumuman',
                'meta_description' => 'Berita dan pengumuman terbaru dari kantor redaksi.',
                'body'             => '<p>Belum ada pengumuman saat ini. Silakan periksa kembali nanti untuk pembaruan dari tim redaksi kami.</p>',
                'extra' => [
                    'items' => [],
                ],
            ],

            'call-for-papers' => [
                'title'            => 'Panggilan untuk Makalah',
                'meta_description' => 'Kirimkan penelitian Anda ke terbitan kami yang akan datang.',
                'body'             => '<h3>Panggilan Makalah Terbuka</h3>
<p>Kami saat ini menerima pengiriman naskah untuk terbitan kami yang akan datang. Kami mengundang artikel penelitian asli, makalah ulasan, dan komunikasi singkat di semua bidang dalam ruang lingkup kami.</p>
<h3>Batas Waktu Pengiriman</h3>
<p>Manuskrip dapat dikirimkan kapan saja. Kami beroperasi berdasarkan pengiriman bergulir (rolling submission).</p>',
                'extra' => [
                    'deadline'    => '',
                    'volume'      => '',
                    'issue'       => '',
                    'theme'       => '',
                ],
            ],
        ];
    }

    /**
     * Ambil data halaman dari DB, atau gunakan default jika tidak ada.
     */
    public static function getPage(string $slug): array
    {
        $page    = static::findBySlug($slug);
        $defaults = static::defaults();
        $default  = $defaults[$slug] ?? ['title' => ucwords(str_replace('-', ' ', $slug)), 'body' => '', 'meta_description' => '', 'extra' => []];

        if (! $page) {
            return array_merge($default, ['slug' => $slug, 'from_db' => false]);
        }

        return [
            'slug'             => $page->slug,
            'title'            => $page->title ?: $default['title'],
            'body'             => $page->body  ?: ($default['body'] ?? ''),
            'meta_description' => $page->meta_description ?: ($default['meta_description'] ?? ''),
            'extra'            => array_merge($default['extra'] ?? [], $page->extra ?? []),
            'from_db'          => true,
            'model'            => $page,
        ];
    }
}
