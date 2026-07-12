@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Process</div>
                <h1 class="hero-title">Peer Review <span class="accent">Process</span></h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'We utilize a stringent Double-Blind Peer Review system to ensure objective evaluation of every manuscript.' }}</p>
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
                    <h3 style="font-weight: 700; margin-bottom: 16px;">What is Double-Blind Review?</h3>
                    <p style="color: var(--text-muted); font-size: 15px; line-height: 1.8; margin: 0;">
                        In a double-blind peer review process, both the reviewer and the author remain anonymous to each other throughout the entire process. This method prevents any reviewer bias based on an author's country of origin, institutional affiliation, or past publication record.
                    </p>
                </div>
            </div>
        </div>

        {{-- Visual Workflow Timeline --}}
        <h3 class="text-center mb-5" style="font-weight: 800; color: var(--text-main);" data-aos="fade-up">Publication Workflow</h3>
        
        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-8">
                
                {{-- Timeline Item 1 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">1</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Manuscript Submission</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">The corresponding author submits the paper through our online system. Automated checks for plagiarism (similarity &lt; 20%) are conducted.</p>
                    </div>
                </div>

                {{-- Timeline Item 2 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">2</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Initial Editorial Screening</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">The Editor-in-Chief assesses the manuscript for fit with the journal's focus and scope, as well as basic formatting requirements.</p>
                    </div>
                </div>

                {{-- Timeline Item 3 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">3</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Peer Review (Double-Blind)</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">The manuscript is sent to at least two independent expert reviewers. This phase typically takes 3-4 weeks.</p>
                    </div>
                </div>

                {{-- Timeline Item 4 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">4</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Revision & Decision</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Based on reviewer feedback, the author may need to make revisions (Minor/Major). The final decision (Accept/Reject) is made by the Editor.</p>
                    </div>
                </div>

                {{-- Timeline Item 5 --}}
                <div class="d-flex mb-4">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0; margin-right: 24px; z-index: 2;">5</div>
                    <div class="pub-card flex-grow-1" style="margin-top: -10px;">
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Copyediting & Publication</h5>
                        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Once accepted, the manuscript undergoes layout formatting, proofreading, and is finally published online with a registered DOI.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
