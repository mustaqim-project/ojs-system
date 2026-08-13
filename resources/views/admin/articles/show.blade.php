@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Artikel','href'=>route('admin.articles.index')],['label'=>Str::limit($article->title,50)]]"/>
    <h1 class="ds-page-title" style="font-size:20px;max-width:700px;line-height:1.4;">{{ $article->title }}</h1>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <a href="{{ route('admin.export.article',$article) }}" class="ds-btn ds-btn-out ds-btn-sm">
      <i class="bi bi-file-earmark-code"></i> Ekspor XML
    </a>
    <x-status-badge :status="$article->status" :label="$article->status_label"/>
  </div>
</div>

{{-- Publish CTA --}}
@if($article->canBePublished())
<div class="ds-alert ds-alert-success" data-aos="fade-up" style="border-radius:10px;padding:20px 24px;margin-bottom:24px;">
  <i class="bi bi-check-circle-fill" style="font-size:18px;"></i>
  <div style="flex:1;">
    <div style="font-weight:700;font-size:14px;">Siap Diterbitkan</div>
    <div style="font-size:13px;opacity:0.8;margin-top:2px;">Pembayaran telah diverifikasi. Silakan pilih nomor terbitan (issue) untuk menerbitkan manuskrip ini.</div>
  </div>
  <button class="ds-btn ds-btn-suc" data-bs-toggle="modal" data-bs-target="#publishModal">
    <i class="bi bi-rocket-takeoff-fill"></i> Terbitkan Sekarang
  </button>
</div>
<div class="modal fade" id="publishModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px;border:1px solid var(--border);">
      <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px;">
        <h5 class="modal-title" style="font-weight:700;font-size:15px;"><i class="bi bi-rocket-takeoff-fill me-2" style="color:var(--success);"></i>Terbitkan Artikel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:24px;">
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Pilih terbitan (issue) untuk menempatkan artikel ini:</p>
        <form method="POST" action="{{ route('admin.articles.publish',$article) }}" id="publishForm">
          @csrf
          <x-ui.form-field label="Terbitan (Issue)" required :error="$errors->first('issue_id')">
            <x-ui.select name="issue_id" required :error="$errors->has('issue_id')">
              @foreach($issues as $issue)
                <option value="{{ $issue->id }}">{{ $issue->display_title }} — {{ $issue->journal->title }}</option>
              @endforeach
            </x-ui.select>
          </x-ui.form-field>
        </form>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;">
        <button type="button" class="ds-btn ds-btn-out" data-bs-dismiss="modal">Batal</button>
        <button type="submit" form="publishForm" class="ds-btn ds-btn-suc">
          <i class="bi bi-rocket-takeoff-fill"></i> Terbitkan
        </button>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row g-3">
  {{-- Main Content --}}
  <div class="col-12 col-lg-8">
    <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Detail Manuskrip</span>
        <div style="display:flex;gap:8px;">
          @if($article->manuscript_file)
            <a href="{{ asset($article->manuscript_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
              <i class="bi bi-download"></i> Manuskrip
            </a>
          @endif
          @if($article->revision_file)
            <a href="{{ asset($article->revision_file) }}" target="_blank" class="ds-btn ds-btn-sm" style="background:var(--warning-bg);color:var(--warning);border-color:var(--warning);">
              <i class="bi bi-download"></i> Revisi
            </a>
          @endif
        </div>
      </div>
      <div>
        @php
        $rows = [
          'Jurnal'     => $article->journal->title,
          'Penulis'    => $article->author->name.' <span style="color:var(--text-muted);font-size:12px;">('.$article->author->email.')</span>',
          'Bahasa'     => strtoupper($article->language ?? 'id'),
        ];
        if($article->assignedEditor) $rows['Editor yang Ditugaskan'] = $article->assignedEditor->name;
        if($article->issue) $rows['Terbitan (Issue)'] = $article->issue->display_title;
        @endphp
        @foreach($rows as $k => $v)
        <div style="display:grid;grid-template-columns:160px 1fr;gap:8px;padding:13px 24px;border-bottom:1px solid #F1F5F9;font-size:14px;">
          <span style="font-weight:500;color:var(--text-muted);">{{ $k }}</span>
          <span style="color:var(--text-main);">{!! $v !!}</span>
        </div>
        @endforeach

        {{-- DOI --}}
        @if($article->doi)
        <div style="display:grid;grid-template-columns:160px 1fr;gap:8px;padding:13px 24px;border-bottom:1px solid #F1F5F9;font-size:14px;">
          <span style="font-weight:500;color:var(--text-muted);">DOI</span>
          <span style="font-family:monospace;color:var(--primary);">{{ $article->doi }}</span>
        </div>
        @endif

        {{-- Abstract --}}
        <div style="padding:16px 24px;border-bottom:1px solid #F1F5F9;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Abstrak</div>
          <p style="font-size:14px;color:var(--text-main);line-height:1.75;margin:0;">{{ $article->abstract }}</p>
        </div>

        {{-- Keywords --}}
        <div style="padding:16px 24px;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Kata Kunci</div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($article->keywords_array as $kw)
              <span style="font-size:12px;background:#F1F5F9;color:#475569;padding:3px 10px;border-radius:20px;border:1px solid #E2E8F0;">{{ $kw }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Reviews --}}
    @if($article->reviews->count())
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-top:16px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Tinjauan Sejawat ({{ $article->reviews->count() }})</span>
      </div>
      @foreach($article->reviews as $review)
      <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:8px;background:#FAF5FF;color:#6B46C1;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">{{ strtoupper(substr($review->reviewer->name,0,1)) }}</div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-main);">{{ $review->reviewer->name }}</div>
              <div style="font-size:12px;color:var(--text-muted);">{{ $review->reviewer->affiliation }}</div>
            </div>
          </div>
          <div style="display:flex;gap:6px;">
            <x-status-badge :status="$review->status"/>
            @if($review->recommendation)
              <x-status-badge :status="$review->recommendation" :label="$review->recommendation_label"/>
            @endif
          </div>
        </div>
        @if($review->average_score)
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">Skor Rata-rata: <strong style="color:var(--text-main);">{{ $review->average_score }}/5</strong></div>
        @endif
        @if($review->comments_to_author)
          <div style="background:var(--bg-app);border-radius:8px;padding:12px;font-size:13px;color:var(--text-main);line-height:1.65;margin-bottom:6px;">
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:6px;">Komentar untuk Penulis</div>
            {{ $review->comments_to_author }}
          </div>
        @endif
        @if($review->comments_to_editor)
          <div style="background:#FEFCE8;border-radius:8px;padding:12px;font-size:13px;color:#713F12;line-height:1.65;">
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Rahasia untuk Editor</div>
            {{ $review->comments_to_editor }}
          </div>
        @endif
      </div>
      @endforeach
    </div>
    @endif

    {{-- Payment --}}
    @if($article->payment)
    <div class="ds-card" data-aos="fade-up" data-aos-delay="300" style="margin-top:16px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Informasi Pembayaran</span>
        <a href="{{ route('admin.payments.show',$article->payment) }}" class="ds-btn ds-btn-ghost ds-btn-sm">
          Kelola <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      <div style="padding:20px 24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div>
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:4px;">Kode Invoice</div>
          <div style="font-family:monospace;font-size:14px;font-weight:700;color:var(--text-main);">{{ $article->payment->invoice_code }}</div>
        </div>
        <div style="width:1px;height:36px;background:var(--border);"></div>
        <div>
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:4px;">Nominal</div>
          <div style="font-size:20px;font-weight:800;color:var(--text-main);">Rp {{ number_format($article->payment->amount,0,',','.') }}</div>
        </div>
        <div style="margin-left:auto;">
          <x-status-badge :status="$article->payment->status" :label="$article->payment->status_label"/>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <div class="col-12 col-lg-4">
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:16px;">
      {{-- Quick Info --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="200">
        <div class="ds-card-hdr"><span class="ds-card-title">Sekilas Info</span></div>
        <div>
          @php
          $meta = [
            'Status'       => null,
            'Tinjauan'     => $article->reviews->count().' penelaah',
            'Dikirim'      => $article->submitted_at?->format('d M Y H:i'),
            'Diterima'     => $article->accepted_at?->format('d M Y'),
            'Diterbitkan'  => $article->published_at?->format('d M Y'),
          ];
          if($article->pages_start) $meta['Halaman'] = $article->pages_start.'–'.$article->pages_end;
          @endphp
          <div style="padding:13px 20px;border-bottom:1px solid #F1F5F9;">
            <span style="font-size:12px;color:var(--text-muted);">Status</span><br>
            <div style="margin-top:6px;"><x-status-badge :status="$article->status" :label="$article->status_label"/></div>
          </div>
          @foreach($meta as $mk => $mv)
            @if($mv)
            <div style="display:grid;grid-template-columns:100px 1fr;gap:8px;padding:10px 20px;border-bottom:1px solid #F1F5F9;font-size:13px;">
              <span style="color:var(--text-muted);">{{ $mk }}</span>
              <span style="color:var(--text-main);font-weight:500;">{{ $mv }}</span>
            </div>
            @endif
          @endforeach
        </div>
      </div>

      {{-- Metadata & DOI --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="300">
        <div class="ds-card-hdr">
          <span class="ds-card-title"><i class="bi bi-link-45deg me-1" style="color:var(--primary);"></i>Metadata & DOI</span>
        </div>
        <div style="padding:20px;">
          <form method="POST" action="{{ route('admin.articles.update-metadata',$article) }}" novalidate>
            @csrf
            <x-ui.form-field label="DOI" hint="Contoh: 10.12345/journal.2026.001">
              <x-ui.input type="text" name="doi" :value="$article->doi" placeholder="10.xxxxx/..."/>
            </x-ui.form-field>
            <div class="row g-2">
              <div class="col-6">
                <x-ui.form-field label="Halaman Mulai">
                  <x-ui.input type="number" name="pages_start" :value="$article->pages_start" placeholder="1"/>
                </x-ui.form-field>
              </div>
              <div class="col-6">
                <x-ui.form-field label="Halaman Akhir">
                  <x-ui.input type="number" name="pages_end" :value="$article->pages_end" placeholder="20"/>
                </x-ui.form-field>
              </div>
            </div>
            <button type="submit" class="ds-btn ds-btn-pri w-100 justify-content-center">
              <i class="bi bi-floppy"></i> Simpan Metadata
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
