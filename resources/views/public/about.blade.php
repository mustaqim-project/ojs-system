@extends('layouts.app')
@section('title', $page['title'] ?? 'Tentang')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Informasi</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Tentang <span class="accent">Jurnal</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Memajukan batas-batas pengetahuan ilmiah melalui penerbitan ilmiah yang ketat, transparan, dan dapat diakses secara global.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="pub-card mb-4" data-aos="fade-up">
                    <div class="prose" style="color:var(--text-muted);line-height:1.8;">
                        {!! $page['body'] !!}
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="pub-card" style="position: sticky; top: 100px;" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="mb-4" style="font-weight: 700; font-size: 16px;">Detail Jurnal</h4>
                    <ul class="list-unstyled" style="margin: 0;">
                        @if(!empty($page['extra']['publisher']))
                        <li style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--border);">
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Penerbit</div>
                            <div style="font-size:14px;font-weight:500;color:var(--text-main);">{{ $page['extra']['publisher'] }}</div>
                        </li>
                        @endif
                        @if(!empty($page['extra']['issn_print']) || !empty($page['extra']['issn_online']))
                        <li style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--border);">
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">ISSN / e-ISSN</div>
                            <div style="font-size:14px;font-weight:500;color:var(--text-main);">{{ $page['extra']['issn_print'] ?? '—' }} / {{ $page['extra']['issn_online'] ?? '—' }}</div>
                        </li>
                        @endif
                        @if(!empty($page['extra']['frequency']))
                        <li style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--border);">
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Frekuensi Publikasi</div>
                            <div style="font-size:14px;font-weight:500;color:var(--text-main);">{{ $page['extra']['frequency'] }}</div>
                        </li>
                        @endif
                        @if(!empty($page['extra']['founded_year']))
                        <li>
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Didirikan</div>
                            <div style="font-size:14px;font-weight:500;color:var(--text-main);">{{ $page['extra']['founded_year'] }}</div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
