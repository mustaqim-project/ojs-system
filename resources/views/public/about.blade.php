@extends('layouts.app')
@section('title', $page['title'] ?? 'About')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Information</div>
                <h1 class="hero-title">{{ $page['title'] ?? 'About the <span class="accent">Journal</span>' }}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Advancing the frontiers of scientific knowledge through rigorous, transparent, and globally accessible scholarly publishing.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                @if(!empty($page['body']))
                <div class="pub-card mb-4" data-aos="fade-up">
                    <div class="prose" style="color:var(--text-muted);line-height:1.8;">
                        {!! $page['body'] !!}
                    </div>
                </div>
                @else
                {{-- Fallback default content --}}
                <div class="pub-card mb-4" data-aos="fade-up">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);">Vision & Mission</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">
                        Our vision is to be the premier open-access platform for groundbreaking research, fostering an inclusive ecosystem where knowledge is shared without boundaries.
                    </p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="pub-card" style="position: sticky; top: 100px;" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="mb-4" style="font-weight: 700; font-size: 16px;">Journal Details</h4>
                    <ul class="list-unstyled" style="margin: 0;">
                        @if(!empty($page['extra']['publisher']))
                        <li style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--border);">
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Publisher</div>
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
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Publication Frequency</div>
                            <div style="font-size:14px;font-weight:500;color:var(--text-main);">{{ $page['extra']['frequency'] }}</div>
                        </li>
                        @endif
                        @if(!empty($page['extra']['founded_year']))
                        <li>
                            <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Founded</div>
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
