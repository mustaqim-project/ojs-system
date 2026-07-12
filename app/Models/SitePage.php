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
        return Cache::remember("site_page_{$slug}", 3600, function () use ($slug) {
            return static::where('slug', $slug)->where('is_active', true)->first();
        });
    }

    /**
     * Hapus cache saat model disimpan / dihapus.
     */
    protected static function booted(): void
    {
        static::saved(fn ($m) => Cache::forget("site_page_{$m->slug}"));
        static::deleted(fn ($m) => Cache::forget("site_page_{$m->slug}"));
    }

    // ===========================================================
    // DEFAULT CONTENT — fallback jika belum diisi di DB
    // ===========================================================

    public static function defaults(): array
    {
        return [
            'about' => [
                'title'            => 'About This Journal',
                'meta_description' => 'Learn about our journal, its mission, vision, and publication history.',
                'body'             => '<h2>About Us</h2>
<p>This journal is an open-access, peer-reviewed publication dedicated to advancing knowledge across multiple disciplines. Since our founding, we have been committed to publishing high-quality research that contributes to the global academic community.</p>
<h3>Our Mission</h3>
<p>To promote the dissemination of scientific knowledge through rigorous peer review and open access publishing, making research freely available to scholars and the public worldwide.</p>
<h3>Our Vision</h3>
<p>To be a leading international journal recognized for academic excellence, integrity, and innovation in scholarly communication.</p>',
                'extra' => [
                    'founded_year'     => '2010',
                    'issn_print'       => '0000-0000',
                    'issn_online'      => '0000-0001',
                    'publisher'        => 'OJS Publishing',
                    'frequency'        => 'Quarterly',
                ],
            ],

            'editorial-team' => [
                'title'            => 'Editorial Team',
                'meta_description' => 'Meet the editorial board and team behind the journal.',
                'body'             => '<p>Our editorial team consists of distinguished scholars and practitioners from around the world, committed to maintaining the highest standards of academic peer review.</p>',
                'extra' => [
                    'editor_in_chief' => ['name' => 'Prof. Dr. John Smith', 'affiliation' => 'University of Science', 'email' => 'editor@journal.com', 'orcid' => ''],
                    'section_editors'  => [
                        ['name' => 'Dr. Jane Doe', 'affiliation' => 'MIT, USA', 'area' => 'Computer Science'],
                        ['name' => 'Dr. Ahmad Fauzi', 'affiliation' => 'UI, Indonesia', 'area' => 'Information Systems'],
                        ['name' => 'Dr. Maria Santos', 'affiliation' => 'USP, Brazil', 'area' => 'Data Engineering'],
                    ],
                ],
            ],

            'reviewer-board' => [
                'title'            => 'Reviewer Board',
                'meta_description' => 'Our expert reviewer board who ensure quality peer review.',
                'body'             => '<p>We are grateful to our dedicated reviewer board members who voluntarily contribute their expertise to maintain the quality of published research.</p>',
                'extra' => [
                    'reviewers' => [
                        ['name' => 'Dr. Reviewer One', 'affiliation' => 'Harvard University, USA', 'area' => 'Machine Learning'],
                        ['name' => 'Dr. Reviewer Two', 'affiliation' => 'Oxford University, UK', 'area' => 'Algorithms'],
                        ['name' => 'Dr. Reviewer Three', 'affiliation' => 'ETH Zurich, Switzerland', 'area' => 'Systems'],
                        ['name' => 'Dr. Reviewer Four', 'affiliation' => 'NUS, Singapore', 'area' => 'Databases'],
                        ['name' => 'Dr. Reviewer Five', 'affiliation' => 'IIT Delhi, India', 'area' => 'Networks'],
                        ['name' => 'Dr. Reviewer Six', 'affiliation' => 'UGM, Indonesia', 'area' => 'Software Engineering'],
                    ],
                ],
            ],

            'author-guidelines' => [
                'title'            => 'Author Guidelines',
                'meta_description' => 'Complete submission guidelines for authors.',
                'body'             => '<h3>Submission Requirements</h3>
<ul>
  <li>Manuscripts must be original and not under consideration elsewhere.</li>
  <li>File format: Microsoft Word (.docx) or RTF.</li>
  <li>Length: 4,000 – 8,000 words (excluding references).</li>
  <li>Abstract: 150–250 words with 3–5 keywords.</li>
  <li>Use APA 7th Edition for citations and references.</li>
</ul>
<h3>Formatting</h3>
<p>Font: Times New Roman 12pt, double-spaced, A4 paper with 2.5 cm margins. Use the IMRaD structure (Introduction, Methods, Results, Discussion).</p>
<h3>Figures &amp; Tables</h3>
<p>All figures and tables must be numbered sequentially and cited in the text. High-resolution images (min 300 DPI) required.</p>',
                'extra' => [
                    'template_url' => '',
                    'apc_waiver'   => 'Waivers are available for authors from low-income countries.',
                ],
            ],

            'ethics' => [
                'title'            => 'Publication Ethics',
                'meta_description' => 'Our commitment to ethical publishing practices.',
                'body'             => '<h3>Editorial Standards</h3>
<p>This journal follows the <a href="https://publicationethics.org/" target="_blank">COPE (Committee on Publication Ethics)</a> guidelines for responsible publication practices.</p>
<h3>Authorship</h3>
<p>All listed authors must have made significant contributions to the research. Guest or ghost authorship is not permitted.</p>
<h3>Plagiarism</h3>
<p>All manuscripts are screened for plagiarism using automated tools. Plagiarism in any form is strictly prohibited.</p>
<h3>Conflicts of Interest</h3>
<p>Authors must disclose any financial or personal relationships that could be perceived as influencing their work.</p>',
                'extra' => [],
            ],

            'peer-review' => [
                'title'            => 'Peer Review Process',
                'meta_description' => 'Transparent, double-blind peer review process.',
                'body'             => '<h3>Double-Blind Review</h3>
<p>This journal uses double-blind peer review, where both authors and reviewers remain anonymous throughout the review process.</p>
<h3>Review Timeline</h3>
<ul>
  <li><strong>Initial editorial check:</strong> 1–2 weeks</li>
  <li><strong>Peer review:</strong> 4–8 weeks</li>
  <li><strong>Revision &amp; decision:</strong> 2–4 weeks</li>
  <li><strong>Publication after acceptance:</strong> 2–4 weeks</li>
</ul>
<h3>Review Criteria</h3>
<p>Reviewers evaluate manuscripts based on originality, methodology, significance of results, clarity of writing, and adherence to ethical standards.</p>',
                'extra' => [],
            ],

            'focus-and-scope' => [
                'title'            => 'Focus and Scope',
                'meta_description' => 'The scope and focus areas of our journal.',
                'body'             => '<h3>Scope</h3>
<p>This journal publishes peer-reviewed research across a broad range of disciplines, with a focus on interdisciplinary work that bridges theory and practice.</p>
<h3>Topics of Interest</h3>
<ul>
  <li>Computer Science &amp; Artificial Intelligence</li>
  <li>Information Systems &amp; Technology</li>
  <li>Data Science &amp; Analytics</li>
  <li>Software Engineering &amp; Development</li>
  <li>Cybersecurity &amp; Privacy</li>
  <li>Human-Computer Interaction</li>
</ul>',
                'extra' => [],
            ],

            'journal-policies' => [
                'title'            => 'Journal Policies',
                'meta_description' => 'Access policies and archiving policies.',
                'body'             => '<h3>Open Access Policy</h3>
<p>This is an open-access journal which means that all content is freely available without charge. Users are allowed to read, download, copy, distribute, print, search, or link to the full texts of the articles.</p>
<h3>Self-Archiving Policy</h3>
<p>Authors may self-archive the accepted manuscript (post-print) in their institutional repository immediately upon acceptance.</p>
<h3>Article Processing Charges (APC)</h3>
<p>This journal charges an Article Processing Charge (APC) upon acceptance. Waivers are available. See our APC policy for details.</p>',
                'extra' => [],
            ],

            'indexing' => [
                'title'            => 'Indexing & Abstracting',
                'meta_description' => 'Databases and indexes that cover our journal.',
                'body'             => '<p>This journal is indexed and abstracted in the following databases:</p>',
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
                'title'            => 'Contact Us',
                'meta_description' => 'Get in touch with the editorial team.',
                'body'             => '<p>We welcome queries regarding manuscript submissions, peer review, and general journal matters.</p>',
                'extra' => [
                    'email'   => 'editor@journal.com',
                    'phone'   => '+62 21 1234 5678',
                    'address' => 'Jl. Contoh No. 1, Jakarta 12345, Indonesia',
                    'maps_embed_url' => '',
                ],
            ],

            'privacy-policy' => [
                'title'            => 'Privacy Policy',
                'meta_description' => 'How we collect, use, and protect your personal data.',
                'body'             => '<h3>1. Introduction</h3>
<p>The names and email addresses entered in this journal site will be used exclusively for the stated purposes of this journal and will not be made available for any other purpose or to any other party.</p>
<h3>2. Data We Collect</h3>
<p>We collect information you provide during registration, submission, and review processes, including your name, email address, affiliation, and manuscript content.</p>
<h3>3. How We Use Your Data</h3>
<p>Your data is used solely to manage the editorial workflow, communicate with you about your submissions, and improve our services.</p>',
                'extra' => [],
            ],

            'terms-conditions' => [
                'title'            => 'Terms & Conditions',
                'meta_description' => 'Terms and conditions for using this platform.',
                'body'             => '<h3>1. Acceptance of Terms</h3>
<p>By registering, accessing, or using this scholarly publishing platform, you agree to comply with and be bound by these Terms and Conditions.</p>
<h3>2. User Responsibilities</h3>
<p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
<h3>3. Intellectual Property</h3>
<p>Published articles are protected under Creative Commons licenses as specified in each article. The platform design and software remain the property of the publisher.</p>',
                'extra' => [],
            ],

            'announcements' => [
                'title'            => 'Announcements',
                'meta_description' => 'Latest news and announcements from the editorial office.',
                'body'             => '<p>No announcements at this time. Check back soon for updates from our editorial team.</p>',
                'extra' => [
                    'items' => [],
                ],
            ],

            'call-for-papers' => [
                'title'            => 'Call for Papers',
                'meta_description' => 'Submit your research to our upcoming issues.',
                'body'             => '<h3>Open Call for Papers</h3>
<p>We are currently accepting submissions for our upcoming issues. We invite original research articles, review papers, and short communications in all areas within our scope.</p>
<h3>Submission Deadline</h3>
<p>Manuscripts may be submitted at any time. We operate on a rolling submission basis.</p>',
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
