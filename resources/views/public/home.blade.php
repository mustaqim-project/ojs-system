@extends('layouts.app')
@section('content')

{{-- ── HERO SECTION ── --}}
<section class="hero" style="position:relative; overflow:hidden;">
  <div class="container" style="max-width:1400px; position:relative; z-index:2;">
    <div class="row align-items-center g-5">
      <div class="col-12 col-lg-7">
        <div class="hero-tag mb-3" data-aos="fade-up" data-aos-delay="100">
          <i class="bi bi-patch-check-fill text-primary"></i> Premier Academic Publisher
        </div>
        <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200" style="font-size:3.5rem; line-height:1.2; font-weight:800; letter-spacing:-0.03em;">
          {!! $global_settings['hero_title'] ?? 'Advance Knowledge.<br>Publish with <span class="accent">Excellence.</span>' !!}
        </h1>
        <p class="hero-desc" data-aos="fade-up" data-aos-delay="300" style="font-size:1.1rem; color:var(--text-muted); max-width:600px; margin:24px 0;">
          {{ $global_settings['hero_subtitle'] ?? $siteDescription ?: 'Enterprise-grade scholarly publishing platform offering rigorous peer review, transparent workflows, and global reach for researchers and academicians.' }}
        </p>
        
        <div class="d-flex flex-wrap gap-3 mt-4" data-aos="fade-up" data-aos-delay="400">
          <a href="{{ $global_settings['hero_button_link'] ?? route('register') }}" class="btn btn-primary shadow" style="padding:12px 28px; font-weight:600; font-size:16px; border-radius:8px;">
            {{ $global_settings['hero_button_text'] ?? 'Submit Manuscript' }} <i class="bi bi-arrow-right ms-2"></i>
          </a>
          <a href="{{ route('public.articles.index') }}" class="btn btn-light shadow-sm" style="padding:12px 28px; font-weight:600; font-size:16px; border-radius:8px; border:1px solid var(--border);">
            Browse Articles <i class="bi bi-journal-text ms-2"></i>
          </a>
        </div>

        <div class="mt-5" data-aos="fade-up" data-aos-delay="500">
          <form action="{{ route('public.search') }}" method="GET" class="d-flex" style="max-width:500px; position:relative;">
            <i class="bi bi-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
            <input type="text" name="q" class="form-control form-control-lg shadow-sm" placeholder="Search for articles, authors, or keywords..." style="padding-left:44px; border-radius:8px; font-size:15px; border-color:var(--border);">
            <button type="submit" class="btn btn-primary" style="position:absolute; right:6px; top:6px; bottom:6px; border-radius:6px; font-weight:600;">Search</button>
          </form>
        </div>
      </div>
      
      <div class="col-12 col-lg-5 d-none d-lg-block" data-aos="zoom-in" data-aos-delay="300">
        <div style="position:relative; width:100%; height:500px;">
          <div style="position:absolute; inset:0; background:radial-gradient(circle at center, var(--primary-light) 0%, transparent 70%); opacity:0.3; border-radius:50%;"></div>
          {{-- Animated Abstract Graphic --}}
          <div style="position:absolute; right:20px; top:50px; width:340px; background:var(--bg-app); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.08); z-index:2;" class="hover-shadow">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
              <span style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Latest Publication</span>
              <span class="badge bg-success" style="font-size:10px;">Peer Reviewed</span>
            </div>
            @if($latestArticles->isNotEmpty())
              @php $featured = $latestArticles->first(); @endphp
              <h5 style="font-weight:800; font-size:18px; line-height:1.4; margin-bottom:12px;">{{ Str::limit($featured->title, 70) }}</h5>
              <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:32px; height:32px; background:var(--primary-light); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px;">
                  {{ substr($featured->author->name ?? 'A', 0, 1) }}
                </div>
                <div>
                  <div style="font-size:13px; font-weight:600;">{{ $featured->author->name ?? 'Unknown Author' }}</div>
                  <div style="font-size:11px; color:var(--text-muted);">Published: {{ $featured->published_at ? $featured->published_at->format('M d, Y') : 'N/A' }}</div>
                </div>
              </div>
              <a href="{{ route('public.articles.show', $featured->slug ?? '') }}" class="btn btn-sm btn-outline-primary w-100" style="border-radius:6px; font-weight:600;">Read Article</a>
            @else
              <p class="text-muted small">No articles published yet.</p>
            @endif
          </div>
          {{-- Floating Badge --}}
          <div style="position:absolute; left:40px; bottom:120px; background:var(--bg-app); border:1px solid var(--border); border-radius:50px; padding:12px 24px; display:flex; align-items:center; gap:12px; box-shadow:0 10px 30px rgba(0,0,0,0.05); z-index:3; animation: float 6s ease-in-out infinite;">
            <i class="bi bi-globe-americas text-primary" style="font-size:24px;"></i>
            <div>
              <div style="font-size:12px; font-weight:700; color:var(--text-muted);">Indexed in</div>
              <div style="font-size:15px; font-weight:800; color:var(--text-main);">Crossref & DOAJ</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── STATISTICS (COUNTUP) ── --}}
<section class="section" style="background:var(--bg-surface); border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1200px;">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
        <h2 class="display-5" style="font-weight:800; color:var(--primary);" data-countup="{{ $totalPublished }}">0</h2>
        <p style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">Published Articles</p>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
        <h2 class="display-5" style="font-weight:800; color:var(--primary);" data-countup="150">+0</h2>
        <p style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">Active Authors</p>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
        <h2 class="display-5" style="font-weight:800; color:var(--primary);" data-countup="45">0</h2>
        <p style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">Global Reviewers</p>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
        <h2 class="display-5" style="font-weight:800; color:var(--primary);" data-countup="28">0</h2>
        <p style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">Days to First Decision</p>
      </div>
    </div>
  </div>
</section>

{{-- ── CURRENT ISSUE & LATEST ARTICLES ── --}}
<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1400px;">
    
    <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
      <div>
        <div class="section-tag">New Releases</div>
        <h2 class="section-title mb-0">Latest Articles</h2>
      </div>
      <a href="{{ route('public.articles.index') }}" class="btn btn-light" style="font-weight:600; border-radius:20px; padding:8px 20px;">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4">
      @forelse($latestArticles as $index => $article)
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
          <div class="pub-card d-flex flex-column" style="height:100%; position:relative; padding:24px;">
            <div style="font-size:12px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Research Article</div>
            <h4 style="font-weight:700; font-size:18px; line-height:1.4; margin-bottom:12px;">
              <a href="{{ route('public.articles.show', $article->slug ?? '') }}" style="text-decoration:none; color:var(--text-main);" class="hover-primary">{{ Str::limit($article->title, 75) }}</a>
            </h4>
            <p style="color:var(--text-muted); font-size:14px; line-height:1.6; margin-bottom:20px; flex-grow:1;">
              {{ Str::limit(strip_tags($article->abstract), 120) }}
            </p>
            
            <div style="display:flex; flex-direction:column; gap:16px;">
              <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:var(--bg-surface); border:1px solid var(--border); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:var(--text-main);">
                  {{ substr($article->author->name ?? 'A', 0, 1) }}
                </div>
                <div style="font-size:13px; font-weight:600; color:var(--text-main);">
                  {{ $article->author->name ?? 'Unknown Author' }}
                  <div style="color:var(--text-muted); font-weight:400; font-size:11px;">University Institute</div>
                </div>
              </div>
              
              <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:12px;">
                <div style="display:flex; gap:16px; font-size:12px; color:var(--text-muted);">
                  <span title="Views"><i class="bi bi-eye me-1"></i> {{ rand(100, 1500) }}</span>
                  <span title="Downloads"><i class="bi bi-download me-1"></i> {{ rand(50, 500) }}</span>
                </div>
                <a href="{{ route('public.articles.show', $article->slug ?? '') }}" style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none;">Read More <i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center text-muted py-5">
          <i class="bi bi-journal-x" style="font-size:48px;"></i>
          <p class="mt-3">No articles published yet.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>

{{-- ── FOCUS & SCOPE / WHY PUBLISH WITH US ── --}}
<section class="section" style="background:var(--bg-surface);">
  <div class="container" style="max-width:1400px;">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5" data-aos="fade-right">
        <div class="section-tag">Benefits</div>
        <h2 class="section-title mb-4">Why Publish With Us?</h2>
        <p class="section-desc mb-4">We are dedicated to providing authors with an exceptional publishing experience, ensuring your research reaches the global scientific community effectively.</p>
        
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:16px;">
          <li style="display:flex; gap:16px; align-items:flex-start;">
            <div style="width:28px; height:28px; border-radius:50%; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="bi bi-check2"></i></div>
            <div>
              <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Rigorous Double-Blind Review</h5>
              <p style="font-size:14px; color:var(--text-muted); margin:0;">Ensures objective and high-quality assessment of all manuscripts.</p>
            </div>
          </li>
          <li style="display:flex; gap:16px; align-items:flex-start;">
            <div style="width:28px; height:28px; border-radius:50%; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="bi bi-check2"></i></div>
            <div>
              <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Global Indexing</h5>
              <p style="font-size:14px; color:var(--text-muted); margin:0;">Indexed in Google Scholar, Crossref (DOI), DOAJ, and Scopus.</p>
            </div>
          </li>
          <li style="display:flex; gap:16px; align-items:flex-start;">
            <div style="width:28px; height:28px; border-radius:50%; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="bi bi-check2"></i></div>
            <div>
              <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">True Open Access</h5>
              <p style="font-size:14px; color:var(--text-muted); margin:0;">Immediate, free access to your work under CC BY-SA 4.0 license.</p>
            </div>
          </li>
          <li style="display:flex; gap:16px; align-items:flex-start;">
            <div style="width:28px; height:28px; border-radius:50%; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="bi bi-check2"></i></div>
            <div>
              <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Fast Turnaround</h5>
              <p style="font-size:14px; color:var(--text-muted); margin:0;">Average time to first decision is 28 days without compromising quality.</p>
            </div>
          </li>
        </ul>
      </div>
      
      <div class="col-lg-7" data-aos="fade-left">
        <div class="row g-4">
          <div class="col-sm-6">
            <div class="pub-card text-center hover-shadow" style="padding:40px 24px; transition:transform 0.3s;">
              <i class="bi bi-cpu text-primary mb-3" style="font-size:40px;"></i>
              <h5 style="font-weight:700;">Computer Science</h5>
              <p style="font-size:13px; color:var(--text-muted); margin:0;">AI, Software Engineering, Networks</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pub-card text-center hover-shadow" style="padding:40px 24px; transition:transform 0.3s;">
              <i class="bi bi-heart-pulse text-danger mb-3" style="font-size:40px;"></i>
              <h5 style="font-weight:700;">Health Sciences</h5>
              <p style="font-size:13px; color:var(--text-muted); margin:0;">Public Health, Clinical, BioMed</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pub-card text-center hover-shadow" style="padding:40px 24px; transition:transform 0.3s;">
              <i class="bi bi-globe text-success mb-3" style="font-size:40px;"></i>
              <h5 style="font-weight:700;">Social Sciences</h5>
              <p style="font-size:13px; color:var(--text-muted); margin:0;">Economics, Education, Sociology</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pub-card text-center hover-shadow" style="padding:40px 24px; transition:transform 0.3s;">
              <i class="bi bi-lightning-charge text-warning mb-3" style="font-size:40px;"></i>
              <h5 style="font-weight:700;">Engineering</h5>
              <p style="font-size:13px; color:var(--text-muted); margin:0;">Mechanical, Electrical, Materials</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── WORKFLOW TIMELINE ── --}}
<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag">Process</div>
      <h2 class="section-title">Publication Workflow</h2>
      <p class="section-desc mx-auto">Our transparent 4-step process from submission to publication.</p>
    </div>
    
    <div class="row g-4 position-relative">
      <!-- Line connecting steps (Desktop only) -->
      <div class="d-none d-lg-block" style="position:absolute; top:24px; left:10%; right:10%; height:2px; background:var(--border); z-index:1;"></div>
      
      @php
      $workflow = [
        ['icon'=>'bi-cloud-arrow-up', 'title'=>'1. Submission', 'desc'=>'Author submits manuscript via OJS.'],
        ['icon'=>'bi-search', 'title'=>'2. Screening & Review', 'desc'=>'Plagiarism check and double-blind peer review.'],
        ['icon'=>'bi-pencil-square', 'title'=>'3. Revision', 'desc'=>'Author revises based on reviewer feedback.'],
        ['icon'=>'bi-globe', 'title'=>'4. Publication', 'desc'=>'Copyediting, layout, and global release.']
      ];
      @endphp
      
      @foreach($workflow as $index => $step)
      <div class="col-12 col-md-6 col-lg-3 text-center position-relative" style="z-index:2;" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
        <div style="width:50px; height:50px; background:white; border:2px solid var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:20px; color:var(--primary); box-shadow:0 4px 10px rgba(0,0,0,0.05);">
          <i class="{{ $step['icon'] }}"></i>
        </div>
        <h5 style="font-weight:700; font-size:16px;">{{ $step['title'] }}</h5>
        <p style="font-size:13px; color:var(--text-muted);">{{ $step['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── JOURNAL METRICS & INDEXING ── --}}
<section class="section" style="background:var(--bg-surface); border-top:1px solid var(--border);">
  <div class="container" style="max-width:1400px;">
    <div class="row g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <h3 style="font-weight:800; margin-bottom:24px;">Journal Metrics 2026</h3>
        <div class="row g-3">
          <div class="col-sm-6">
            <div style="background:var(--bg-app); border:1px solid var(--border); padding:20px; border-radius:var(--radius-md);">
              <div style="font-size:13px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Acceptance Rate</div>
              <div style="font-size:28px; font-weight:800; color:var(--primary);">34%</div>
            </div>
          </div>
          <div class="col-sm-6">
            <div style="background:var(--bg-app); border:1px solid var(--border); padding:20px; border-radius:var(--radius-md);">
              <div style="font-size:13px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Days to First Decision</div>
              <div style="font-size:28px; font-weight:800; color:var(--primary);">28</div>
            </div>
          </div>
          <div class="col-sm-6">
            <div style="background:var(--bg-app); border:1px solid var(--border); padding:20px; border-radius:var(--radius-md);">
              <div style="font-size:13px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Days to Publication</div>
              <div style="font-size:28px; font-weight:800; color:var(--primary);">75</div>
            </div>
          </div>
          <div class="col-sm-6">
            <div style="background:var(--bg-app); border:1px solid var(--border); padding:20px; border-radius:var(--radius-md);">
              <div style="font-size:13px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Downloads (Yearly)</div>
              <div style="font-size:28px; font-weight:800; color:var(--primary);">45K+</div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6" data-aos="fade-left">
        <h3 style="font-weight:800; margin-bottom:24px;">Indexed & Abstracted In</h3>
        <div class="row g-3">
          @php
          $indexes = ['Google Scholar', 'Crossref', 'DOAJ', 'Scopus', 'SINTA', 'Dimensions'];
          @endphp
          @foreach($indexes as $idx)
          <div class="col-4 col-sm-4">
            <div style="background:var(--bg-app); border:1px solid var(--border); padding:20px 10px; border-radius:var(--radius-md); text-align:center; height:100%; display:flex; align-items:center; justify-content:center;">
              <span style="font-weight:700; font-size:14px; color:var(--text-main);">{{ $idx }}</span>
            </div>
          </div>
          @endforeach
        </div>
        <div class="mt-4 text-end">
          <a href="{{ route('public.indexing') }}" style="font-weight:600; color:var(--primary); text-decoration:none;">View all indexing <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── NEWSLETTER & CALL FOR PAPERS CTA ── --}}
<section class="section text-center" style="background:var(--primary); color:white;">
  <div class="container" style="max-width:800px;" data-aos="zoom-in">
    <h2 style="font-weight:800; margin-bottom:16px;">Ready to Publish Your Research?</h2>
    <p style="font-size:18px; opacity:0.9; margin-bottom:32px;">Join thousands of authors who have published their groundbreaking research with us. Submissions are open for the next issue.</p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="{{ route('register') }}" class="btn btn-light" style="font-weight:700; font-size:16px; padding:12px 32px; border-radius:8px; color:var(--primary);">Submit Manuscript</a>
      <a href="{{ route('public.author-guidelines') }}" class="btn btn-outline-light" style="font-weight:600; font-size:16px; padding:12px 32px; border-radius:8px;">Read Guidelines</a>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  // Simple CountUp animation script
  document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-countup]');
    
    const animateCount = (el) => {
      const target = parseInt(el.getAttribute('data-countup').replace(/[^0-9]/g, ''));
      const text = el.getAttribute('data-countup');
      const suffix = text.replace(/[0-9]/g, '');
      const duration = 2000;
      const step = target / (duration / 16);
      let current = 0;
      
      const timer = setInterval(() => {
        current += step;
        if(current >= target) {
          clearInterval(timer);
          el.innerText = target + suffix;
        } else {
          el.innerText = Math.floor(current) + suffix;
        }
      }, 16);
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting) {
          animateCount(entry.target);
          observer.unobserve(entry.target);
        }
      });
    });

    counters.forEach(c => observer.observe(c));
  });
</script>
@endsection
