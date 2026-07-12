@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Panduan</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Panduan <span class="accent">Penulis</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Semua yang perlu Anda ketahui untuk mempersiapkan dan mengirimkan naskah Anda.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5 pb-5 mb-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                
                @if(!empty($page['body']))
                <div class="pub-card mb-4" data-aos="fade-up">
                    <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
                </div>
                @else
                {{-- Submission Checklist --}}
                <div class="pub-card mb-4" data-aos="fade-up">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-ui-checks text-primary me-2"></i> Daftar Periksa Penyerahan</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">Sebelum mengirimkan naskah Anda, pastikan naskah tersebut memenuhi semua persyaratan berikut:</p>
                    <ul class="list-group list-group-flush mt-3" style="font-size: 15px; color: var(--text-main);">
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> Kiriman tersebut belum pernah dipublikasikan sebelumnya, dan juga tidak sedang dipertimbangkan oleh jurnal lain.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> File naskah dalam format dokumen OpenOffice, Microsoft Word, atau RTF.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> Bila tersedia, URL dan DOI untuk referensi telah disertakan.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> Teks tersebut mematuhi persyaratan gaya dan bibliografi yang diuraikan dalam pedoman ini.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> Surat pengantar disertakan, ditujukan kepada editor dan merinci kebaruan penelitian.</li>
                    </ul>
                </div>

                {{-- Manuscript Formatting --}}
                <div class="pub-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-file-earmark-text text-primary me-2"></i> Pemformatan Naskah</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">Naskah harus ditulis dalam bahasa Inggris (atau Indonesia) yang jelas dan ringkas. Struktur artikel biasanya harus mengikuti format IMRaD (Pendahuluan, Metode, Hasil, dan Pembahasan).</p>
                    
                    <h5 class="mt-4 mb-3" style="font-weight: 600;">Halaman Judul</h5>
                    <p style="color: var(--text-muted); line-height: 1.8;">Halaman judul harus mencantumkan judul artikel, nama lengkap penulis, afiliasi, dan alamat email penulis korespondensi. Abstrak 150-250 kata dan 3-5 kata kunci juga harus disertakan.</p>

                    <h5 class="mt-4 mb-3" style="font-weight: 600;">Gambar & Tabel</h5>
                    <p style="color: var(--text-muted); line-height: 1.8;">Semua gambar dan tabel harus dikutip dalam teks dan diberi nomor secara berurutan. Gambar beresolusi tinggi (minimal 300 DPI) harus diunggah sebagai file terpisah selama pengiriman jika diperlukan.</p>
                </div>

                {{-- Reference Style --}}
                <div class="pub-card" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-journal-bookmark text-primary me-2"></i> Gaya Referensi</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">Kami menggunakan gaya <strong>APA (American Psychological Association) Edisi ke-7</strong> untuk kutipan dan referensi. Penulis sangat didorong untuk menggunakan perangkat lunak manajemen referensi seperti Mendeley, Zotero, atau EndNote.</p>
                    
                    <div style="background: var(--bg-app); padding: 20px; border-radius: 8px; margin-top: 20px;">
                        <h6 style="font-weight: 700; margin-bottom: 12px; font-size: 14px;">Contoh Kutipan Artikel Jurnal:</h6>
                        <p style="font-size: 14px; color: var(--text-muted); font-family: monospace; margin: 0;">Grady, J. S., Her, M., Moreno, G., Perez, C., & Yelinek, J. (2019). Emotions in storybooks: A comparison of storybooks that represent ethnic and racial groups in the United States. <em>Psychology of Popular Media Culture, 8</em>(3), 207–217. https://doi.org/10.1037/ppm0000185</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div style="position: sticky; top: 100px; z-index: 10;">
                    <div class="pub-card text-center" data-aos="fade-up" data-aos-delay="300">
                    <div style="width: 64px; height: 64px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 24px;">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <h4 class="mb-3" style="font-weight: 700; font-size: 18px;">Siap Mengirimkan?</h4>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">Unduh template naskah resmi kami untuk memastikan format Anda benar sebelum dikirimkan.</p>
                    
                    <a href="{{ !empty($page['extra']['template_url']) ? $page['extra']['template_url'] : '#' }}"
                       class="btn btn-secondary w-100 mb-3" style="height: 44px;"
                       {{ !empty($page['extra']['template_url']) ? 'target="_blank"' : '' }}>
                        <i class="bi bi-file-word me-2"></i> Unduh Template (.docx)
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100" style="height: 44px;">
                        Buat Kiriman <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
