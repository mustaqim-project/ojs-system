@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Editor'],['label'=>'Articles','href'=>route('editor.articles.index')],['label'=>Str::limit($article->title,45)]]"/>
    <h1 class="ds-page-title" style="font-size:19px;max-width:680px;line-height:1.4;">{{ $article->title }}</h1>
  </div>
  <x-status-badge :status="$article->status" :label="$article->status_label"/>
</div>

<div class="row g-3">
  {{-- Main Content --}}
  <div class="col-12 col-xl-8">

    {{-- Article Details --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Manuscript Information</span>
        <div style="display:flex;gap:8px;">
          @if($article->manuscript_file)
            <a href="{{ asset('storage/'.$article->manuscript_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
              <i class="bi bi-download"></i> Manuscript
            </a>
          @endif
          @if($article->revision_file)
            <a href="{{ asset('storage/'.$article->revision_file) }}" target="_blank" class="ds-btn ds-btn-sm" style="background:var(--warning-bg);color:var(--warning);border-color:var(--warning);">
              <i class="bi bi-download"></i> Revision
            </a>
          @endif
        </div>
      </div>
      <div>
        @php
        $rows = [
          'Journal' => $article->journal->title,
          'Author'  => $article->author->name.($article->author->affiliation ? ' <span style="color:var(--text-muted);font-size:12px;">· '.$article->author->affiliation.'</span>' : ''),
          'Submitted'=> $article->submitted_at?->format('d M Y H:i'),
        ];
        if($article->author_note) $rows["Author's Note"] = '<em style="color:var(--text-muted);">'.$article->author_note.'</em>';
        @endphp
        @foreach($rows as $k => $v)
        <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;padding:12px 24px;border-bottom:1px solid #F1F5F9;font-size:14px;">
          <span style="font-weight:500;color:var(--text-muted);">{{ $k }}</span>
          <span style="color:var(--text-main);">{!! $v !!}</span>
        </div>
        @endforeach
        {{-- Abstract --}}
        <div style="padding:16px 24px;border-bottom:1px solid #F1F5F9;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Abstract</div>
          <p style="font-size:14px;color:var(--text-main);line-height:1.75;margin:0;">{{ $article->abstract }}</p>
        </div>
        {{-- Keywords --}}
        <div style="padding:16px 24px;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Keywords</div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($article->keywords_array as $kw)
              <span style="font-size:12px;background:#F1F5F9;color:#475569;padding:3px 10px;border-radius:20px;border:1px solid #E2E8F0;">{{ $kw }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Peer Reviews --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-top:16px;">
      <div class="ds-card-hdr"><span class="ds-card-title">Peer Reviews ({{ $article->reviews->count() }})</span></div>
      @forelse($article->reviews as $review)
      <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:8px;background:#FAF5FF;color:#6B46C1;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($review->reviewer->name,0,1)) }}
            </div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-main);">{{ $review->reviewer->name }}</div>
              <div style="font-size:12px;color:var(--text-muted);">{{ $review->reviewer->affiliation }}</div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
            <x-status-badge :status="$review->status" :label="ucfirst($review->status)"/>
            @if($review->recommendation)
              <x-status-badge :status="$review->recommendation" :label="$review->recommendation_label"/>
            @endif
          </div>
        </div>
        @if($review->status === 'completed')
          @if($review->average_score)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
              <span style="font-size:12px;color:var(--text-muted);">Score:</span>
              @for($s=1;$s<=5;$s++)
                <i class="bi bi-star-fill" style="font-size:13px;color:{{ $s <= $review->average_score ? '#F59E0B' : '#E2E8F0' }};"></i>
              @endfor
              <span style="font-size:12px;font-weight:600;color:var(--text-main);">{{ $review->average_score }}/5</span>
            </div>
          @endif
          @if($review->comments_to_author)
            <div style="background:var(--bg-app);border-radius:8px;padding:12px;font-size:13px;color:var(--text-main);line-height:1.65;margin-bottom:6px;">
              <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:6px;">Comments to Author</div>
              {{ $review->comments_to_author }}
            </div>
          @endif
          @if($review->comments_to_editor)
            <div style="background:#FEFCE8;border-radius:8px;padding:12px;font-size:13px;color:#713F12;line-height:1.65;">
              <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Confidential to Editor</div>
              {{ $review->comments_to_editor }}
            </div>
          @endif
        @else
          <div style="font-size:12px;color:var(--text-muted);">Review due: {{ $review->due_date?->format('d M Y') }}</div>
        @endif
      </div>
      @empty
      <x-ui.empty-state icon="bi-clipboard" title="No reviewers assigned" description="Assign reviewers using the panel on the right."/>
      @endforelse
    </div>
  </div>

  {{-- Right Panel --}}
  <div class="col-12 col-xl-4">
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:16px;">

      {{-- Assign Reviewer --}}
      @if(in_array($article->status, ['submitted','under_review','revision_required']))
      <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr">
          <span class="ds-card-title"><i class="bi bi-person-check me-2" style="color:var(--primary);"></i>Assign Reviewer</span>
        </div>
        <div style="padding:20px;">
          <form method="POST" action="{{ route('editor.articles.assign-reviewer',$article) }}" novalidate>
            @csrf
            <x-ui.form-field label="Select Reviewer" required>
              <x-ui.select name="reviewer_id" required>
                @foreach($reviewers as $rv)
                  <option value="{{ $rv->id }}">{{ $rv->name }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
            <button type="submit" class="ds-btn ds-btn-pri w-100 justify-content-center">
              <i class="bi bi-person-plus"></i> Assign Reviewer
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Editorial Decision --}}
      @if(in_array($article->status, ['submitted','under_review','revision_required']))
      <div class="ds-card" data-aos="fade-up" data-aos-delay="200">
        <div class="ds-card-hdr">
          <span class="ds-card-title"><i class="bi bi-gavel me-2" style="color:var(--warning);"></i>Editorial Decision</span>
        </div>
        <div style="padding:20px;">
          <form method="POST" action="{{ route('editor.articles.decision',$article) }}" novalidate>
            @csrf
            <x-ui.form-field label="Decision" required>
              <x-ui.select name="decision" required>
                <option value="accept">✅ Accept — Publish this manuscript</option>
                <option value="revision">🔄 Revision — Request revision</option>
                <option value="reject">❌ Reject — Decline manuscript</option>
              </x-ui.select>
            </x-ui.form-field>
            <x-ui.form-field label="Note to Author" hint="Explain decision, revision instructions, etc.">
              <x-ui.textarea name="editor_note" rows="3" placeholder="Please address the following concerns..."></x-ui.textarea>
            </x-ui.form-field>
            <button type="submit" onclick="return confirm('Are you sure about this editorial decision?')"
                    class="ds-btn ds-btn-danger w-100 justify-content-center">
              <i class="bi bi-gavel"></i> Submit Decision
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Payment Info --}}
      @if($article->payment)
      <div class="ds-card" data-aos="fade-up" data-aos-delay="300">
        <div class="ds-card-hdr">
          <span class="ds-card-title">APC Payment</span>
          <x-status-badge :status="$article->payment->status" :label="$article->payment->status_label"/>
        </div>
        <div style="padding:16px 20px;">
          <div style="font-size:18px;font-weight:800;color:var(--text-main);">Rp {{ number_format($article->payment->amount,0,',','.') }}</div>
          <div style="font-size:12px;font-family:monospace;color:var(--text-muted);margin-top:4px;">{{ $article->payment->invoice_code }}</div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection
