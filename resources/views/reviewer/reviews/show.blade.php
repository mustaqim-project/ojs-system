@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Reviewer Portal'],['label'=>'Review Assignments','href'=>route('reviewer.reviews.index')],['label'=>'Review Form']]"/>
    <h1 class="ds-page-title">Review Assignment</h1>
  </div>
  @php
  $sc = [
    'pending'    => ['bg'=>'var(--warning-bg)','color'=>'var(--warning)'],
    'in_progress'=> ['bg'=>'var(--info-bg)','color'=>'var(--info)'],
    'completed'  => ['bg'=>'var(--success-bg)','color'=>'var(--success)'],
    'accepted'   => ['bg'=>'var(--success-bg)','color'=>'var(--success)'],
    'declined'   => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)'],
  ];
  $style = $sc[$review->status] ?? ['bg'=>'var(--bg-app)','color'=>'var(--text-muted)'];
  @endphp
  <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $style['bg'] }};color:{{ $style['color'] }};">
    {{ ucfirst(str_replace('_',' ',$review->status)) }}
  </span>
</div>

<div class="row g-3">
  {{-- Main Content: Article + Form --}}
  <div class="col-12 col-lg-8">

    {{-- Article Summary --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="100" style="margin-bottom:20px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Manuscript for Review</span>
        <div style="display:flex;gap:8px;">
          <a href="{{ asset($review->article->manuscript_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
            <i class="bi bi-download"></i> Manuscript
          </a>
          @if($review->article->revision_file)
          <a href="{{ asset($review->article->revision_file) }}" target="_blank" class="ds-btn ds-btn-sm" style="background:var(--warning-bg);color:var(--warning);border-color:var(--warning);">
            <i class="bi bi-download"></i> Revision
          </a>
          @endif
        </div>
      </div>
      <div style="padding:24px;">
        <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
          <span style="font-size:11px;font-weight:700;color:var(--primary);background:var(--primary-light);padding:3px 10px;border-radius:20px;">{{ $review->article->journal->title }}</span>
          <span style="font-size:11px;font-weight:700;color:var(--success);background:var(--success-bg);padding:3px 10px;border-radius:20px;">{{ strtoupper($review->article->language) }}</span>
        </div>
        <h3 style="font-size:18px;font-weight:700;color:var(--text-main);letter-spacing:-0.02em;margin-bottom:12px;line-height:1.4;">{{ $review->article->title }}</h3>
        <div style="font-size:14px;color:var(--text-main);line-height:1.75;margin-bottom:16px;">{!! $review->article->abstract !!}</div>
        
        @if($review->article->keywords)
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
          @foreach($review->article->keywords_array as $kw)
            <span style="font-size:12px;background:#F1F5F9;color:#475569;padding:3px 10px;border-radius:20px;border:1px solid #E2E8F0;">{{ $kw }}</span>
          @endforeach
        </div>
        @endif
      </div>

      @if($review->due_date)
      <div style="padding:14px 24px;border-top:1px solid var(--border);background:var(--bg-app);display:flex;align-items:center;gap:8px;">
        @php $ov = $review->due_date->isPast() && $review->status !== 'completed'; @endphp
        <i class="bi bi-clock" style="color:{{ $ov ? 'var(--danger)' : 'var(--text-muted)' }};"></i>
        <span style="font-size:13px;color:{{ $ov ? 'var(--danger)' : 'var(--text-main)' }};font-weight:{{ $ov ? '700' : '500' }};">
          Review Due: {{ $review->due_date->format('d M Y') }}
          @if($ov) <span style="margin-left:4px;">(Overdue!)</span> @endif
        </span>
      </div>
      @endif
    </div>

    {{-- Accept/Decline Action (pending) --}}
    @if($review->status === 'pending')
    <div class="row g-3" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:20px;">
      <div class="col-md-6">
        <div style="background:var(--success-bg);border:1px solid #C6F6D5;border-radius:10px;padding:24px;height:100%;display:flex;flex-direction:column;">
          <h4 style="font-size:14px;font-weight:700;color:var(--success);margin-bottom:10px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-check-circle-fill" style="font-size:18px;"></i> Accept Assignment
          </h4>
          <p style="font-size:13px;color:#276749;margin-bottom:20px;line-height:1.6;flex:1;">I agree to peer review this manuscript and can complete it by the requested deadline.</p>
          <form method="POST" action="{{ route('reviewer.reviews.accept', $review) }}">
            @csrf
            <button type="submit" class="ds-btn ds-btn-suc w-100 justify-content-center" style="height:42px;">
              <i class="bi bi-check-lg"></i> Accept Request
            </button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background:var(--danger-bg);border:1px solid #FED7D7;border-radius:10px;padding:24px;height:100%;display:flex;flex-direction:column;">
          <h4 style="font-size:14px;font-weight:700;color:var(--danger);margin-bottom:10px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-x-circle-fill" style="font-size:18px;"></i> Decline Assignment
          </h4>
          <p style="font-size:13px;color:#9B2C2C;margin-bottom:20px;line-height:1.6;flex:1;">I am unable to review this manuscript (e.g., conflict of interest, lack of time/expertise).</p>
          <form method="POST" action="{{ route('reviewer.reviews.decline', $review) }}">
            @csrf
            <button type="submit" onclick="return confirm('Are you sure you want to decline this review assignment?')" class="ds-btn ds-btn-danger w-100 justify-content-center" style="height:42px;">
              <i class="bi bi-x-lg"></i> Decline Request
            </button>
          </form>
        </div>
      </div>
    </div>
    @endif

    {{-- Review Form (in_progress / accepted) --}}
    @if(in_array($review->status, ['in_progress', 'accepted']))
    <div class="ds-section" data-aos="fade-up" data-aos-delay="300">
      <div class="ds-section-hdr">
        <span class="ds-section-title"><i class="bi bi-pencil-square me-2" style="color:var(--primary);"></i>Submit Peer Review</span>
      </div>
      <div class="ds-section-body">
        <form method="POST" action="{{ route('reviewer.reviews.submit', $review) }}" enctype="multipart/form-data" novalidate>
          @csrf

          {{-- Recommendation --}}
          <div style="margin-bottom:24px;">
            <label style="font-size:13px;font-weight:600;color:var(--text-main);display:block;margin-bottom:12px;">Recommendation <span style="color:var(--danger);">*</span></label>
            <div class="row g-3">
              @php
              $recs = [
                'accept' =>['label'=>'Accept','sub'=>'Publish without revisions','bg'=>'var(--success-bg)','border'=>'#C6F6D5','ic'=>'var(--success)','icon'=>'bi-check-circle-fill'],
                'minor'  =>['label'=>'Minor Revision','sub'=>'Requires small corrections','bg'=>'var(--warning-bg)','border'=>'#FEEBC8','ic'=>'var(--warning)','icon'=>'bi-arrow-clockwise'],
                'major'  =>['label'=>'Major Revision','sub'=>'Requires substantial changes','bg'=>'var(--warning-bg)','border'=>'#FBD38D','ic'=>'#D97706','icon'=>'bi-exclamation-triangle-fill'],
                'reject' =>['label'=>'Reject','sub'=>'Not suitable for publication','bg'=>'var(--danger-bg)','border'=>'#FED7D7','ic'=>'var(--danger)','icon'=>'bi-x-circle-fill'],
              ];
              @endphp
              @foreach($recs as $val => $r)
              <div class="col-sm-6">
                <label style="display:flex;align-items:flex-start;gap:12px;padding:16px;border:2px solid {{ old('recommendation')===$val?$r['border']:'var(--border)' }};border-radius:8px;cursor:pointer;background:{{ old('recommendation')===$val?$r['bg']:'var(--bg-surface)' }};transition:all 0.2s;"
                       onmouseover="this.style.borderColor='{{ $r['border'] }}'" onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--border)';this.style.background='var(--bg-surface)'}">
                  <input type="radio" name="recommendation" value="{{ $val }}" {{ old('recommendation')===$val?'checked':'' }} required style="margin-top:2px;accent-color:{{ $r['ic'] }};width:16px;height:16px;"
                         onchange="document.querySelectorAll('.rec-label').forEach(l=>{l.style.borderColor='var(--border)';l.style.background='var(--bg-surface)'});this.closest('label').style.borderColor='{{ $r['border'] }}';this.closest('label').style.background='{{ $r['bg'] }}'"/>
                  <div>
                    <div style="font-size:14px;font-weight:700;color:{{ $r['ic'] }};display:flex;align-items:center;gap:6px;margin-bottom:2px;"><i class="{{ $r['icon'] }}"></i>{{ $r['label'] }}</div>
                    <div style="font-size:12px;color:var(--text-muted);line-height:1.4;">{{ $r['sub'] }}</div>
                  </div>
                </label>
              </div>
              @endforeach
            </div>
            @error('recommendation')<div style="font-size:12px;color:var(--danger);margin-top:6px;">{{ $message }}</div>@enderror
          </div>

          {{-- Evaluation Scores --}}
          <div style="margin-bottom:24px;">
            <label style="font-size:13px;font-weight:600;color:var(--text-main);display:flex;align-items:center;gap:6px;margin-bottom:10px;">
              Evaluation Scores <span style="font-size:11px;font-weight:normal;color:var(--text-muted);">(1–5, optional)</span>
            </label>
            <div style="background:var(--bg-app);border:1px solid var(--border);border-radius:8px;padding:20px;">
              <div class="row g-4">
                @foreach([
                  'originality_score' => 'Originality & Innovation',
                  'methodology_score' => 'Methodology & Technical Rigor',
                  'relevance_score'   => 'Relevance & Contribution',
                  'writing_score'     => 'Quality of Writing & Presentation'
                ] as $field => $lbl)
                <div class="col-sm-6">
                  <div style="font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:8px;">{{ $lbl }}</div>
                  <div class="rating-stars" style="display:flex;gap:8px;">
                    @for($s=1;$s<=5;$s++)
                    <label style="cursor:pointer;display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--bg-surface);border:1px solid var(--border);transition:all 0.15s;"
                           onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='none'">
                      <input type="radio" name="{{ $field }}" value="{{ $s }}" {{ old($field)==$s?'checked':'' }} style="display:none;"/>
                      <i class="bi bi-star-fill" style="font-size:16px;color:{{ old($field)>=$s?'#F59E0B':'#E2E8F0' }};transition:color 0.15s;"></i>
                    </label>
                    @endfor
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Comments to Author --}}
          <x-ui.form-field label="Comments to Author" required :error="$errors->first('comments_to_author')" hint="Provide constructive feedback on the methodology, results, and writing. This will be visible to the author. (Min 50 characters)">
            <x-ui.textarea name="comments_to_author" rows="8" required :error="$errors->has('comments_to_author')" placeholder="Detailed review outlining strengths, weaknesses, and specific areas for improvement...">{{ old('comments_to_author') }}</x-ui.textarea>
          </x-ui.form-field>

          {{-- Comments to Editor --}}
          <x-ui.form-field label="Confidential Comments to Editor" hint="Optional. Concerns regarding academic misconduct, conflict of interest, or candid remarks. This will NOT be shared with the author.">
            <x-ui.textarea name="comments_to_editor" rows="4" placeholder="Confidential remarks for the editorial team...">{{ old('comments_to_editor') }}</x-ui.textarea>
          </x-ui.form-field>

          {{-- Annotated File --}}
          <x-ui.form-field label="Annotated Manuscript File" hint="Optional. Upload a PDF/DOCX containing your specific comments or highlighted text. Max 10MB.">
            <input type="file" name="review_file" accept=".pdf,.doc,.docx"
                   style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);font-size:13px;color:var(--text-main);cursor:pointer;"/>
          </x-ui.form-field>

          {{-- Submission Warning --}}
          <div class="ds-alert ds-alert-warn" style="margin-bottom:24px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:13px;">Once submitted, your review <strong>cannot be modified</strong>. Please ensure all feedback and recommendations are final.</span>
          </div>

          <button type="submit" onclick="return confirm('Are you sure you want to submit this review? This action cannot be undone.')" class="ds-btn ds-btn-pri w-100 justify-content-center" style="height:48px;font-size:15px;">
            <i class="bi bi-send-fill"></i> Submit Final Review
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- Completed View --}}
    {{-- Review Completed Info --}}
    @if($review->status === 'completed')
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-top:20px;">
      <div class="ds-card-hdr"><span class="ds-card-title">Review Submitted</span></div>
      <div style="padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
          <span style="font-size:13px;color:var(--text-muted);">Recommendation:</span>
          <x-status-badge :status="$review->recommendation" :label="$review->recommendation_label"/>
        </div>

        @if($review->average_score)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
          <span style="font-size:13px;color:var(--text-muted);">Overall Score:</span>
          <div style="display:flex;gap:3px;">
            @for($s=1;$s<=5;$s++)
              <i class="bi bi-star-fill" style="font-size:16px;color:{{ $s <= $review->average_score ? '#F59E0B' : '#E2E8F0' }};"></i>
            @endfor
          </div>
          <span style="font-size:16px;font-weight:800;color:var(--text-main);margin-left:4px;">{{ $review->average_score }}/5</span>
        </div>
        @endif
        
        @if($review->comments_to_author)
        <div style="margin-bottom:20px;">
          <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:10px;">Comments to Author:</div>
          <div style="background:var(--bg-app);border-radius:10px;padding:20px;font-size:14px;color:var(--text-main);line-height:1.7;">{!! $review->comments_to_author !!}</div>
        </div>
        @endif

        <div style="font-size:13px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
          <i class="bi bi-clock-history"></i> Completed on: {{ $review->completed_at?->format('d M Y H:i') }}
        </div>
      </div>
    </div>
    @endif

  </div>

  {{-- Right Sidebar --}}
  <div class="col-12 col-lg-4">
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:20px;">

      {{-- Assignment Info --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Assignment Details</span></div>
        <div>
          @php
          $rows = [
            'Journal' => $review->article->journal->title,
            'Author'  => $review->article->author->name,
            'Assigned'=> $review->created_at->format('d M Y'),
          ];
          if($review->due_date) {
            $ov = $review->due_date->isPast() && $review->status !== 'completed';
            $rows['Deadline'] = '<span style="color:'.($ov?'var(--danger)':'var(--text-main)').';font-weight:600;">'.$review->due_date->format('d M Y').'</span>';
          }
          if($review->completed_at) $rows['Completed'] = $review->completed_at->format('d M Y');
          @endphp
          @foreach($rows as $k => $v)
          <div style="display:grid;grid-template-columns:90px 1fr;gap:8px;padding:12px 20px;border-bottom:1px solid var(--border);font-size:13px;">
            <span style="font-weight:500;color:var(--text-muted);">{{ $k }}</span>
            <span style="color:var(--text-main);">{!! $v !!}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Scoring Guide --}}
      @if(in_array($review->status, ['in_progress', 'accepted']))
      <div class="ds-card" data-aos="fade-up" data-aos-delay="300">
        <div class="ds-card-hdr"><span class="ds-card-title">Scoring Guide</span></div>
        <div style="padding:16px 20px;">
          <ul style="margin:0;padding-left:16px;font-size:13px;color:var(--text-muted);line-height:2;">
            <li><strong style="color:var(--text-main);">5</strong> — Excellent / Outstanding</li>
            <li><strong style="color:var(--text-main);">4</strong> — Good / Above Average</li>
            <li><strong style="color:var(--text-main);">3</strong> — Fair / Acceptable</li>
            <li><strong style="color:var(--text-main);">2</strong> — Poor / Needs Work</li>
            <li><strong style="color:var(--text-main);">1</strong> — Very Poor / Unacceptable</li>
          </ul>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>

@push('scripts')
<script>
// Interactive star rating for review form
document.querySelectorAll('.rating-stars label').forEach(lbl => {
  lbl.addEventListener('mouseover', () => {
    const input = lbl.querySelector('input[type=radio]');
    if (!input) return;
    const name = input.name;
    const val  = parseInt(input.value);
    document.querySelectorAll(`input[name="${name}"]`).forEach((inp, i) => {
      const star = inp.closest('label').querySelector('i');
      if (star) star.style.color = (i < val) ? '#F59E0B' : '#E2E8F0';
    });
  });
  
  const resetStars = (name) => {
    const checked = document.querySelector(`input[name="${name}"]:checked`);
    const val = checked ? parseInt(checked.value) : 0;
    document.querySelectorAll(`input[name="${name}"]`).forEach((inp, i) => {
      const star = inp.closest('label').querySelector('i');
      if (star) star.style.color = (i < val) ? '#F59E0B' : '#E2E8F0';
    });
  };

  lbl.closest('.rating-stars').addEventListener('mouseout', () => {
    const input = lbl.querySelector('input[type=radio]');
    if(input) resetStars(input.name);
  });

  lbl.addEventListener('click', () => {
    const input = lbl.querySelector('input[type=radio]');
    if (!input) return;
    resetStars(input.name);
  });
});
</script>
@endpush
@endsection
