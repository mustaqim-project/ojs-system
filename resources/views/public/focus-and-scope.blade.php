@extends('layouts.app')

@section('title', $page['title'] ?? 'Fokus & Ruang Lingkup')
@section('meta_description', $page['meta_description'] ?? 'Bidang minat dan domain ilmiah yang dicakup oleh jurnal.')

@section('content')

{{-- PAGE HEADER --}}
<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Tentang Jurnal</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Fokus &amp; Ruang Lingkup' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Bidang minat dan domain ilmiah yang dicakup oleh jurnal.' }}</p>
  </div>
</section>

{{-- MAIN CONTENT --}}
<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">
    
    @php
      $activeJournal = \App\Models\Journal::where('is_active', true)->first();
    @endphp

    @if(!empty($page['body']))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
    </div>
    @elseif($activeJournal && !empty($activeJournal->description))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $activeJournal->description !!}</div>
    </div>
    @else
    <div class="pub-card text-center py-5" data-aos="fade-up">
      <i class="bi bi-journal-text" style="font-size:48px; color:var(--text-muted); opacity:0.6;"></i>
      <h5 class="mt-3" style="font-weight:700; color:var(--text-main);">Fokus dan Ruang Lingkup</h5>
      <p class="text-muted" style="max-width:600px; margin:8px auto 0;">Informasi fokus dan ruang lingkup jurnal sedang dalam proses pembaruan oleh dewan redaksi.</p>
    </div>
    @endif

  </div>
</section>

@endsection
