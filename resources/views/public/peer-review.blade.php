@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Proses</div>
                <h1 class="hero-title">Proses Penelaahan <span class="accent">Sejawat</span></h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Kami menggunakan sistem penelaahan sejawat ganda-buta (double-blind) yang ketat untuk memastikan evaluasi objektif setiap naskah.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5">
    <div class="container">
        
        @if(!empty($page['body']))
        <div class="row mb-5"><div class="col-12" data-aos="fade-up">
            <div class="pub-card" style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
        </div></div>
        @endif

        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                <div class="pub-card" style="padding: 32px;">
                    <div style="font-size: 48px; color: var(--primary); margin-bottom: 16px;">
                        <i class="bi bi-eye-slash"></i>
                    </div>
                    <h3 style="font-weight: 700; margin-bottom: 16px;">Apa itu Penelaahan Ganda-Buta (Double-Blind)?</h3>
                    <p style="color: var(--text-muted); font-size: 15px; line-height: 1.8; margin: 0;">
                        Dalam proses penelaahan sejawat ganda-buta (double-blind), baik penelaah maupun penulis tetap anonim satu sama lain di seluruh proses. Metode ini mencegah bias penelaah berdasarkan negara asal penulis, afiliasi institusional, atau catatan publikasi sebelumnya.
                    </p>
                </div>
            </div>
        </div>

        {{-- Visual Workflow Timeline --}}
        <h3 class="text-center mb-5" style="font-weight: 800; color: var(--text-main);" data-aos="fade-up">Alur Kerja Publikasi</h3>
        
        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-8">
                
                {{-- Timeline Item 1 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">1</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Pengiriman Naskah</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Penulis korespondensi mengirimkan makalah melalui sistem online kami. Pemeriksaan otomatis untuk plagiarisme (kesamaan &lt; 20%) dilakukan.</p>
                    </div>
                </div>

                {{-- Timeline Item 2 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">2</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Penyaringan Editorial Awal</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Pemimpin Redaksi menilai naskah apakah sesuai dengan fokus dan ruang lingkup jurnal, serta persyaratan format dasar.</p>
                    </div>
                </div>

                {{-- Timeline Item 3 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">3</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Penelaahan Sejawat Ganda-Buta (Double-Blind)</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Naskah dikirim ke setidaknya dua penelaah ahli independen (mitra bestari). Fase ini biasanya memakan waktu 3-4 minggu.</p>
                    </div>
                </div>

                {{-- Timeline Item 4 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">4</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Revisi & Keputusan</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Berdasarkan umpan balik penelaah, penulis mungkin perlu melakukan revisi (Kecil/Besar). Keputusan akhir (Terima/Tolak) dibuat oleh Editor.</p>
                    </div>
                </div>

                {{-- Timeline Item 5 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">5</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Penyuntingan Naskah & Publikasi</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Setelah diterima, naskah menjalani pemformatan tata letak, penyuntingan naskah (copyediting), pemeriksaan ejaan, dan akhirnya diterbitkan secara online dengan DOI yang terdaftar.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
