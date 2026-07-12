@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Guidelines</div>
                <h1 class="hero-title">{{ $page['title'] ?? 'Author <span class="accent">Guidelines</span>' }}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Everything you need to know to prepare and submit your manuscript.' }}</p>
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
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-ui-checks text-primary me-2"></i> Submission Checklist</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">Before submitting your manuscript, please ensure that it meets all of the following requirements:</p>
                    <ul class="list-group list-group-flush mt-3" style="font-size: 15px; color: var(--text-main);">
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> The submission has not been previously published, nor is it before another journal for consideration.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> The manuscript file is in OpenOffice, Microsoft Word, or RTF document file format.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> Where available, URLs and DOIs for the references have been provided.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> The text adheres to the stylistic and bibliographic requirements outlined in these guidelines.</li>
                        <li class="list-group-item bg-transparent px-0 border-light"><i class="bi bi-check-circle-fill text-success me-2"></i> A cover letter is included, addressing the editor and detailing the novelty of the research.</li>
                    </ul>
                </div>

                {{-- Manuscript Formatting --}}
                <div class="pub-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-file-earmark-text text-primary me-2"></i> Manuscript Formatting</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">Manuscripts should be written in clear and concise English. The structure of the article should typically follow the IMRaD format (Introduction, Methods, Results, and Discussion).</p>
                    
                    <h5 class="mt-4 mb-3" style="font-weight: 600;">Title Page</h5>
                    <p style="color: var(--text-muted); line-height: 1.8;">The title page must include the article title, full author names, affiliations, and the email address of the corresponding author. An abstract of 150-250 words and 3-5 keywords must also be provided.</p>

                    <h5 class="mt-4 mb-3" style="font-weight: 600;">Figures & Tables</h5>
                    <p style="color: var(--text-muted); line-height: 1.8;">All figures and tables must be cited in the text and numbered sequentially. High-resolution images (minimum 300 DPI) should be uploaded as separate files during submission if required.</p>
                </div>

                {{-- Reference Style --}}
                <div class="pub-card" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="mb-4" style="font-weight: 700; color: var(--text-main);"><i class="bi bi-journal-bookmark text-primary me-2"></i> Reference Style</h3>
                    <p style="color: var(--text-muted); line-height: 1.8;">We use the <strong>APA (American Psychological Association) 7th Edition</strong> style for citations and references. Authors are highly encouraged to use reference management software such as Mendeley, Zotero, or EndNote.</p>
                    
                    <div style="background: var(--bg-app); padding: 20px; border-radius: 8px; margin-top: 20px;">
                        <h6 style="font-weight: 700; margin-bottom: 12px; font-size: 14px;">Example Journal Article Citation:</h6>
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
                    <h4 class="mb-3" style="font-weight: 700; font-size: 18px;">Ready to Submit?</h4>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">Download our official manuscript template to ensure your formatting is correct before submission.</p>
                    
                    <a href="{{ !empty($page['extra']['template_url']) ? $page['extra']['template_url'] : '#' }}"
                       class="btn btn-secondary w-100 mb-3" style="height: 44px;"
                       {{ !empty($page['extra']['template_url']) ? 'target="_blank"' : '' }}>
                        <i class="bi bi-file-word me-2"></i> Download Template (.docx)
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100" style="height: 44px;">
                        Make a Submission <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
