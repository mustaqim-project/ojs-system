@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Kebijakan</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Etika <span class="accent">Publikasi</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Kami berkomitmen untuk mempertahankan standar tertinggi etika publikasi.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5">
    <div class="container">
        
        {{-- Dynamic body from DB --}}
        @if(!empty($page['body']))
        <div class="row mb-4">
            <div class="col-12" data-aos="fade-up">
                <div class="pub-card" style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
            </div>
        </div>
        @endif

        <div class="row mb-5">
            <div class="col-12" data-aos="fade-up">
                <div class="pub-card text-center" style="background: var(--primary); border: none; padding: 40px;">
                    <h3 style="color: white; font-weight: 700; margin-bottom: 16px;">Pernyataan Pedoman COPE</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 16px; max-width: 800px; margin: 0 auto; line-height: 1.8;">
                        Jurnal ini dengan ketat mengikuti pedoman dan praktik inti yang ditetapkan oleh <strong>Committee on Publication Ethics (COPE)</strong>. Kami mengharapkan semua pihak yang terlibat dalam proses publikasi—penulis, editor, penelaah, dan penerbit—untuk menyetujui standar perilaku etis yang diharapkan.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Authors --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="pub-card h-100">
                    <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                        <i class="bi bi-pen"></i>
                    </div>
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Tugas Penulis</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Orisinalitas:</strong> Pastikan semua karya sepenuhnya asli. Plagiarisme dalam bentuk apa pun tidak dapat diterima.</li>
                        <li class="mb-2"><strong>Akses Data:</strong> Bersiaplah untuk menyediakan data mentah untuk tinjauan editorial berdasarkan permintaan.</li>
                        <li class="mb-2"><strong>Pengiriman Ganda:</strong> Jangan mengirimkan naskah yang sama ke lebih dari satu jurnal secara bersamaan.</li>
                        <li class="mb-2"><strong>Kepenulisan:</strong> Batasi kepenulisan kepada mereka yang telah memberikan kontribusi signifikan.</li>
                    </ul>
                </div>
            </div>

            {{-- Editors --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="pub-card h-100">
                    <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                        <i class="bi bi-briefcase"></i>
                    </div>
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Tugas Editor</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Keadilan:</strong> Mengevaluasi naskah berdasarkan konten intelektualnya tanpa memandang ras, jenis kelamin, atau kewarganegaraan.</li>
                        <li class="mb-2"><strong>Kerahasiaan:</strong> Jangan mengungkapkan informasi apa pun tentang naskah yang dikirimkan kepada siapa pun selain penulis korespondensi dan penelaah.</li>
                        <li class="mb-2"><strong>Konflik Kepentingan:</strong> Mengundurkan diri dari mempertimbangkan naskah di mana mereka memiliki konflik kepentingan.</li>
                    </ul>
                </div>
            </div>

            {{-- Reviewers --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="pub-card h-100">
                    <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Tugas Penelaah</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Kontribusi:</strong> Tinjauan sejawat membantu editor dalam membuat keputusan editorial dan dapat membantu penulis dalam menyempurnakan makalah.</li>
                        <li class="mb-2"><strong>Kecepatan:</strong> Beri tahu editor segera jika tidak memenuhi syarat atau tidak dapat meninjau dengan segera.</li>
                        <li class="mb-2"><strong>Objektivitas:</strong> Tinjauan harus dilakukan secara objektif. Kritik pribadi terhadap penulis tidak pantas.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
