@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Author Portal'],['label'=>'My Submissions','href'=>route('author.articles.index')],['label'=>'New Submission']]"/>
    <h1 class="ds-page-title">Submit New Manuscript</h1>
    <p class="ds-page-subtitle">Fill in all required information before submitting your manuscript.</p>
  </div>
</div>

<div style="max-width:780px;">
  <form method="POST" action="{{ route('author.articles.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- Step 1: Journal --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="100">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-journal-bookmark me-2" style="color:var(--primary);"></i>Step 1: Select Journal</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Target Journal" required :error="$errors->first('journal_id')">
          <x-ui.select name="journal_id" required :error="$errors->has('journal_id')" placeholder="Select the journal that best fits your manuscript">
            @foreach($journals as $j)
              <option value="{{ $j->id }}" {{ old('journal_id') == $j->id ? 'selected' : '' }}>
                {{ $j->title }}{{ $j->abbreviation ? ' ('.$j->abbreviation.')' : '' }}
              </option>
            @endforeach
          </x-ui.select>
        </x-ui.form-field>
      </div>
    </div>

    {{-- Step 2: Article Info --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="200">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-file-earmark-text me-2" style="color:var(--primary);"></i>Step 2: Manuscript Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Article Title" required :error="$errors->first('title')" hint="Be specific and descriptive. Avoid abbreviations.">
          <x-ui.input type="text" name="title" :value="old('title')" placeholder="A Comprehensive Study of..." required :error="$errors->has('title')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Abstract" required :error="$errors->first('abstract')" hint="Minimum 100 characters. Summarize the problem, method, results, and conclusion.">
          <x-ui.textarea name="abstract" rows="6" placeholder="This study investigates..." required :error="$errors->has('abstract')">{{ old('abstract') }}</x-ui.textarea>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-8">
            <x-ui.form-field label="Keywords" required :error="$errors->first('keywords')" hint="Separate with commas. Maximum 6 keywords.">
              <x-ui.input type="text" name="keywords" :value="old('keywords')" placeholder="machine learning, NLP, deep learning" :error="$errors->has('keywords')"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-4">
            <x-ui.form-field label="Language" required>
              <x-ui.select name="language" required>
                <option value="id" {{ old('language','id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English</option>
              </x-ui.select>
            </x-ui.form-field>
          </div>
        </div>
      </div>
    </div>

    {{-- Step 3: File Upload --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="300">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-upload me-2" style="color:var(--primary);"></i>Step 3: Upload Files</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Manuscript File" required :error="$errors->first('manuscript_file')" hint="Accepted: PDF, DOC, DOCX. Maximum 10MB.">
          <input type="file" name="manuscript_file" accept=".pdf,.doc,.docx" required
                 class="{{ $errors->has('manuscript_file') ? 'is-invalid' : '' }}"
                 style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);font-size:13px;color:var(--text-main);cursor:pointer;"/>
        </x-ui.form-field>
        <x-ui.form-field label="Cover Letter" hint="Optional. Letter of introduction to the editor.">
          <input type="file" name="cover_letter" accept=".pdf,.doc,.docx"
                 style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);font-size:13px;color:var(--text-main);cursor:pointer;"/>
        </x-ui.form-field>
        <x-ui.form-field label="Note to Editor" hint="Optional. Additional information for the editorial team.">
          <x-ui.textarea name="author_note" rows="3" placeholder="Additional context for the editorial team...">{{ old('author_note') }}</x-ui.textarea>
        </x-ui.form-field>
      </div>
    </div>

    {{-- Submission Warning --}}
    <div class="ds-alert ds-alert-info" data-aos="fade-up" style="margin-bottom:24px;">
      <i class="bi bi-info-circle-fill"></i>
      <div style="font-size:13px;line-height:1.6;">Please ensure your manuscript follows the journal's author guidelines before submitting. Once submitted, the manuscript cannot be withdrawn without contacting the editorial office.</div>
    </div>

    <div style="display:flex;gap:12px;">
      <button type="submit" class="ds-btn ds-btn-pri" style="height:44px;padding:0 28px;font-size:15px;">
        <i class="bi bi-send-fill"></i> Submit Manuscript
      </button>
      <a href="{{ route('author.articles.index') }}" class="ds-btn ds-btn-out" style="height:44px;padding:0 20px;">Cancel</a>
    </div>
  </form>
</div>

@endsection
