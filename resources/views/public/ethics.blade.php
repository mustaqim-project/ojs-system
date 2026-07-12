@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Policies</div>
                <h1 class="hero-title">Publication <span class="accent">Ethics</span></h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'We are committed to maintaining the highest standards of publication ethics.' }}</p>
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
                    <h3 style="color: white; font-weight: 700; margin-bottom: 16px;">COPE Guidelines Statement</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 16px; max-width: 800px; margin: 0 auto; line-height: 1.8;">
                        This journal strictly follows the guidelines and core practices set by the <strong>Committee on Publication Ethics (COPE)</strong>. We expect all parties involved in the publication process—authors, editors, reviewers, and the publisher—to agree upon standards of expected ethical behavior.
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
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Duties of Authors</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Originality:</strong> Ensure all work is entirely original. Plagiarism in any form is unacceptable.</li>
                        <li class="mb-2"><strong>Data Access:</strong> Be ready to provide raw data for editorial review upon request.</li>
                        <li class="mb-2"><strong>Multiple Submissions:</strong> Do not submit the same manuscript to more than one journal concurrently.</li>
                        <li class="mb-2"><strong>Authorship:</strong> Limit authorship to those who have made a significant contribution.</li>
                    </ul>
                </div>
            </div>

            {{-- Editors --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="pub-card h-100">
                    <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                        <i class="bi bi-briefcase"></i>
                    </div>
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Duties of Editors</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Fair Play:</strong> Evaluate manuscripts for their intellectual content without regard to race, gender, or citizenship.</li>
                        <li class="mb-2"><strong>Confidentiality:</strong> Do not disclose any information about a submitted manuscript to anyone other than the corresponding author and reviewers.</li>
                        <li class="mb-2"><strong>Conflict of Interest:</strong> Recuse themselves from considering manuscripts in which they have conflicts of interest.</li>
                    </ul>
                </div>
            </div>

            {{-- Reviewers --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="pub-card h-100">
                    <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 style="font-weight: 700; font-size: 18px; margin-bottom: 16px;">Duties of Reviewers</h4>
                    <ul style="color: var(--text-muted); font-size: 14px; line-height: 1.7; padding-left: 20px;">
                        <li class="mb-2"><strong>Contribution:</strong> Peer review assists the editor in making editorial decisions and may assist the author in improving the paper.</li>
                        <li class="mb-2"><strong>Promptness:</strong> Notify the editor immediately if unqualified or unable to review promptly.</li>
                        <li class="mb-2"><strong>Objectivity:</strong> Reviews should be conducted objectively. Personal criticism of the author is inappropriate.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
