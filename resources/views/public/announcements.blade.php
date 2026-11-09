@extends('layouts.app')
@section('title', $page['title'] ?? 'Pengumuman')
@section('meta_description', $page['meta_description'] ?? '')
@section('content')

<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Informasi</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">
      {{ $page['title'] ?? 'Pengumuman' }}
    </h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">
      {{ $page['meta_description'] ?? 'Tetap ikuti berita dan pembaruan terbaru dari dewan redaksi.' }}
    </p>
  </div>
</section>

<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">

    {{-- Dynamic body content --}}
    @if(!empty($page['body']))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
    </div>
    @endif

    {{-- Dynamic announcement items from database --}}
    @php
      $dbAnnouncements = $dbAnnouncements ?? collect();
      $items = $dbAnnouncements->isNotEmpty() ? $dbAnnouncements->map(fn($a) => [
          'title' => $a->title,
          'date' => $a->publish_at ? $a->publish_at->toDateString() : $a->created_at->toDateString(),
          'content' => strip_tags($a->body)
      ])->toArray() : ($page['extra']['items'] ?? []);
    @endphp
    @if(count($items))
      @foreach($items as $i => $item)
      <div class="pub-card mb-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
          <h4 style="font-weight:700; margin:0; color:var(--primary);">{{ $item['title'] ?? '' }}</h4>
          @if(!empty($item['date']))
          <span style="font-size:13px; color:var(--text-muted);"><i class="bi bi-calendar3 me-1"></i>{{ is_numeric(strtotime($item['date'])) ? \Carbon\Carbon::parse($item['date'])->locale('id')->translatedFormat('d F Y') : $item['date'] }}</span>
          @endif
        </div>
        @if(!empty($item['content']))
        <p style="color:var(--text-muted); line-height:1.7;">{{ $item['content'] }}</p>
        @endif
      </div>
      @endforeach
    @elseif(empty($page['body']))
      <div class="col-12 text-center text-muted py-5 pub-card" data-aos="fade-up">
        <i class="bi bi-megaphone" style="font-size:48px;"></i>
        <p class="mt-3 mb-0">Belum ada pengumuman terbaru saat ini.</p>
      </div>
    @endif

  </div>
</section>

@endsection
