@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[
      ['label'=>'Admin'],
      ['label'=>'Site Pages', 'url'=>route('admin.pages.index')],
      ['label'=>$page['label']]
    ]"/>
    <h1 class="ds-page-title">
      <i class="{{ $page['icon'] }} me-2" style="color:var(--primary);"></i>
      Edit: {{ $page['label'] }}
    </h1>
    <p class="ds-page-subtitle">Ubah konten halaman publik <code>/{{ $page['slug'] }}</code></p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="{{ url('/' . $page['slug']) }}" target="_blank" class="ds-btn ds-btn-out">
      <i class="bi bi-eye me-1"></i> Preview
    </a>
    <a href="{{ route('admin.pages.index') }}" class="ds-btn ds-btn-ghost">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>
</div>

@if(session('success'))
<div class="ds-alert ds-alert-suc mb-4" data-aos="fade-up">
  <i class="bi bi-check-circle-fill"></i>
  <div>{{ session('success') }}</div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

<form method="POST" action="{{ route('admin.pages.update', $page['slug']) }}" id="pageForm" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="row g-4">
    {{-- Main Content --}}
    <div class="col-12 col-xl-8" data-aos="fade-up">
      <div class="ds-card">
        <div class="ds-card-hdr">
          <span class="ds-card-title">Page Content</span>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:20px;">

          {{-- Title --}}
          <x-ui.form-field label="Page Title" hint="Judul yang tampil di browser tab dan heading halaman">
            <x-ui.input type="text" name="title" :value="$page['title']" placeholder="e.g. About Our Journal" required/>
          </x-ui.form-field>

          {{-- Meta Description --}}
          <x-ui.form-field label="Meta Description" hint="Deskripsi singkat untuk SEO (maks. 160 karakter)">
            <x-ui.input type="text" name="meta_description" :value="$page['meta_description']" placeholder="Short description for search engines..." maxlength="500"/>
          </x-ui.form-field>

          {{-- Meta Keywords --}}
          <x-ui.form-field label="Meta Keywords" hint="Kata kunci SEO, pisahkan dengan koma (contoh: journal, article, science)">
            <x-ui.input type="text" name="extra[meta_keywords]" :value="$page['extra']['meta_keywords'] ?? ''" placeholder="keyword1, keyword2, keyword3"/>
          </x-ui.form-field>

          {{-- Body --}}
          <x-ui.form-field label="Page Body (HTML)" hint="Konten utama halaman. Mendukung HTML dan formatting.">
            <textarea name="body" id="bodyEditor" rows="18"
                      style="width:100%;font-family:monospace;font-size:13px;padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);color:var(--text-main);resize:vertical;line-height:1.6;">{{ old('body', $page['body']) }}</textarea>
          </x-ui.form-field>

        </div>
      </div>

      {{-- Extra Fields: Contact --}}
      @if($page['slug'] === 'contact')
      <div class="ds-card mt-4" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Contact Details</span></div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
          <x-ui.form-field label="Email">
            <x-ui.input type="email" name="extra[email]" :value="$page['extra']['email'] ?? ''" placeholder="editor@journal.com"/>
          </x-ui.form-field>
          <x-ui.form-field label="Phone">
            <x-ui.input type="text" name="extra[phone]" :value="$page['extra']['phone'] ?? ''" placeholder="+62 21 1234 5678"/>
          </x-ui.form-field>
          <x-ui.form-field label="Address">
            <x-ui.textarea name="extra[address]" rows="3">{{ $page['extra']['address'] ?? '' }}</x-ui.textarea>
          </x-ui.form-field>
          <x-ui.form-field label="Google Maps Embed URL" hint="URL dari Google Maps > Share > Embed a map">
            <x-ui.input type="url" name="extra[maps_embed_url]" :value="$page['extra']['maps_embed_url'] ?? ''" placeholder="https://www.google.com/maps/embed?..."/>
          </x-ui.form-field>
        </div>
      </div>
      @endif

      {{-- Extra Fields: About --}}
      @if($page['slug'] === 'about')
      <div class="ds-card mt-4" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Journal Information</span></div>
        <div style="padding:24px;">
          <div class="row g-3">
            <div class="col-md-6">
              <x-ui.form-field label="Founded Year">
                <x-ui.input type="text" name="extra[founded_year]" :value="$page['extra']['founded_year'] ?? ''"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-6">
              <x-ui.form-field label="Publisher">
                <x-ui.input type="text" name="extra[publisher]" :value="$page['extra']['publisher'] ?? ''"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-6">
              <x-ui.form-field label="ISSN Print">
                <x-ui.input type="text" name="extra[issn_print]" :value="$page['extra']['issn_print'] ?? ''" placeholder="0000-0000"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-6">
              <x-ui.form-field label="ISSN Online">
                <x-ui.input type="text" name="extra[issn_online]" :value="$page['extra']['issn_online'] ?? ''" placeholder="0000-0001"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-6">
              <x-ui.form-field label="Publication Frequency">
                <x-ui.input type="text" name="extra[frequency]" :value="$page['extra']['frequency'] ?? ''" placeholder="Quarterly"/>
              </x-ui.form-field>
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Extra Fields: Author Guidelines --}}
      @if($page['slug'] === 'author-guidelines')
      <div class="ds-card mt-4" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Guidelines Extras</span></div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
          <x-ui.form-field label="Template Download URL (Manual Link)" hint="URL file template naskah dari server luar (opsional)">
            <x-ui.input type="url" name="extra[template_url]" :value="$page['extra']['template_url'] ?? ''" placeholder="https://..."/>
          </x-ui.form-field>
          
          <x-ui.form-field label="Upload Template Baru" hint="Unggah berkas template naskah (.doc, .docx, .pdf, .rtf)">
            <input type="file" name="template_file" class="form-control" accept=".doc,.docx,.pdf,.rtf" style="padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-app); color: var(--text-main);">
            @if(!empty($page['extra']['template_url']))
              <div style="margin-top: 10px; font-size: 13px;">
                <span class="text-muted">Template saat ini:</span> 
                <a href="{{ $page['extra']['template_url'] }}" target="_blank" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                  <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download Template Aktif
                </a>
              </div>
            @endif
          </x-ui.form-field>

          <x-ui.form-field label="APC Waiver Info">
            <x-ui.textarea name="extra[apc_waiver]" rows="2">{{ $page['extra']['apc_waiver'] ?? '' }}</x-ui.textarea>
          </x-ui.form-field>
        </div>
      </div>
      @endif

      {{-- Extra Fields: Call for Papers --}}
      @if($page['slug'] === 'call-for-papers')
      <div class="ds-card mt-4" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Call for Papers Details</span></div>
        <div style="padding:24px;">
          <div class="row g-3">
            <div class="col-md-6">
              <x-ui.form-field label="Submission Deadline">
                <x-ui.input type="date" name="extra[deadline]" :value="$page['extra']['deadline'] ?? ''"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-3">
              <x-ui.form-field label="Volume">
                <x-ui.input type="text" name="extra[volume]" :value="$page['extra']['volume'] ?? ''"/>
              </x-ui.form-field>
            </div>
            <div class="col-md-3">
              <x-ui.form-field label="Issue">
                <x-ui.input type="text" name="extra[issue]" :value="$page['extra']['issue'] ?? ''"/>
              </x-ui.form-field>
            </div>
            <div class="col-12">
              <x-ui.form-field label="Special Theme (optional)">
                <x-ui.input type="text" name="extra[theme]" :value="$page['extra']['theme'] ?? ''" placeholder="e.g. AI in Healthcare"/>
              </x-ui.form-field>
            </div>
          </div>
        </div>
      </div>
      @endif

    </div>

    {{-- Sidebar --}}
    <div class="col-12 col-xl-4" data-aos="fade-up" data-aos-delay="100">

      {{-- Publish Options --}}
      <div class="ds-card mb-4" style="position:sticky;top:80px;">
        <div class="ds-card-hdr"><span class="ds-card-title">Publish Options</span></div>
        <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

          {{-- Status Toggle --}}
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:var(--bg-app);border-radius:var(--radius-sm);border:1px solid var(--border);">
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-main);">Page Status</div>
              <div style="font-size:12px;color:var(--text-muted);">Apakah halaman aktif/tampil?</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <input type="hidden" name="is_active" value="0"/>
              <input type="checkbox" id="is_active" name="is_active" value="1"
                     {{ $page['is_active'] ? 'checked' : '' }}
                     style="width:18px;height:18px;accent-color:var(--primary);cursor:pointer;"/>
              <label for="is_active" style="font-size:13px;color:var(--text-muted);cursor:pointer;margin:0;">Active</label>
            </div>
          </div>

          <button type="submit" class="ds-btn ds-btn-pri w-100" style="height:42px;">
            <i class="bi bi-floppy-fill me-2"></i> Save Changes
          </button>

          @if($page['from_db'] ?? false)
          <button type="button" class="ds-btn ds-btn-ghost w-100" style="height:38px;color:var(--danger);" onclick="if(confirm('Reset ke konten default? Perubahan akan hilang.')) document.getElementById('reset-form').submit();">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Default
          </button>
          @endif

        </div>
      </div>

      {{-- Preview Info --}}
      <div class="ds-card" style="padding:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--text-muted);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.05em;">Public URL</div>
        <code style="font-size:13px;color:var(--primary);word-break:break-all;">
          {{ url('/' . $page['slug']) }}
        </code>
        <div class="mt-3">
          <a href="{{ url('/' . $page['slug']) }}" target="_blank" class="ds-btn ds-btn-ghost ds-btn-sm w-100">
            <i class="bi bi-box-arrow-up-right me-1"></i> Open in new tab
          </a>
        </div>
      </div>

    </div>
  </div>
</form>

@if($page['from_db'] ?? false)
<form id="reset-form" method="POST" action="{{ route('admin.pages.reset', $page['slug']) }}" style="display:none;">
  @csrf
  @method('DELETE')
</form>
@endif

@endsection
