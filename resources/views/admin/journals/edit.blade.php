{{-- admin/journals/edit.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Journals','href'=>route('admin.journals.index')],['label'=>'Edit: '.($journal->abbreviation ?? $journal->title)]]"/>
    <h1 class="ds-page-title">Edit Journal</h1>
    <p class="ds-page-subtitle">{{ $journal->title }}</p>
  </div>
</div>

<div style="max-width:720px;">
  <form method="POST" action="{{ route('admin.journals.update',$journal) }}" enctype="multipart/form-data" novalidate>
    @csrf @method('PUT')

    <div class="ds-section" data-aos="fade-up">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-journal-bookmark me-2"></i>Journal Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Journal Title" required :error="$errors->first('title')">
          <x-ui.input type="text" name="title" :value="old('title',$journal->title)" required :error="$errors->has('title')"/>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Publication Frequency" required>
              <x-ui.select name="frequency" required>
                @foreach(['monthly'=>'Monthly','bimonthly'=>'Bimonthly','quarterly'=>'Quarterly','semiannual'=>'Semiannual','annual'=>'Annual'] as $v => $l)
                  <option value="{{ $v }}" {{ old('frequency',$journal->frequency) === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Chief Editor">
              <x-ui.select name="editor_id">
                <option value="">— None —</option>
                @foreach($editors as $e)
                  <option value="{{ $e->id }}" {{ old('editor_id',$journal->editor_id) == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
        </div>
        <x-ui.form-field label="Journal Description">
          <x-ui.textarea name="description" rows="3">{{ old('description',$journal->description) }}</x-ui.textarea>
        </x-ui.form-field>
        <x-ui.form-field label="Status">
          <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg-app);border:1px solid var(--border);border-radius:var(--radius-sm);">
            <input type="hidden" name="is_active" value="0"/>
            <x-ui.checkbox id="ia" name="is_active" value="1" :checked="old('is_active',$journal->is_active)" label="Journal Active"/>
          </div>
        </x-ui.form-field>
      </div>
    </div>

    <div style="display:flex;gap:12px;">
      <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-check-lg"></i> Save Changes</button>
      <a href="{{ route('admin.journals.index') }}" class="ds-btn ds-btn-out">Cancel</a>
    </div>
  </form>
</div>

@endsection
