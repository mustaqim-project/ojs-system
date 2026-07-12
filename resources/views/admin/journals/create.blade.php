{{-- admin/journals/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Journals','href'=>route('admin.journals.index')],['label'=>'Add New Journal']]"/>
    <h1 class="ds-page-title">Add New Journal</h1>
    <p class="ds-page-subtitle">Register a new scholarly journal on the platform</p>
  </div>
</div>

<div style="max-width:720px;">
  <form method="POST" action="{{ route('admin.journals.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- Basic Info --}}
    <div class="ds-section" data-aos="fade-up">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-journal-bookmark me-2"></i>Journal Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Journal Title" required :error="$errors->first('title')">
          <x-ui.input type="text" name="title" :value="old('title')" placeholder="International Journal of Science & Technology" required :error="$errors->has('title')"/>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-4">
            <x-ui.form-field label="Abbreviation" hint="e.g. IJST">
              <x-ui.input type="text" name="abbreviation" :value="old('abbreviation')" placeholder="IJST"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-4">
            <x-ui.form-field label="ISSN Print" hint="XXXX-XXXX format">
              <x-ui.input type="text" name="issn_print" :value="old('issn_print')" placeholder="2580-XXXX"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-4">
            <x-ui.form-field label="ISSN Online" hint="XXXX-XXXX format">
              <x-ui.input type="text" name="issn_online" :value="old('issn_online')" placeholder="2580-XXXX"/>
            </x-ui.form-field>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Publication Frequency" required :error="$errors->first('frequency')">
              <x-ui.select name="frequency" required :error="$errors->has('frequency')">
                @foreach(['monthly'=>'Monthly','bimonthly'=>'Bimonthly (every 2 months)','quarterly'=>'Quarterly (every 3 months)','semiannual'=>'Semiannual (every 6 months)','annual'=>'Annual'] as $v => $l)
                  <option value="{{ $v }}" {{ old('frequency') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Chief Editor">
              <x-ui.select name="editor_id">
                <option value="">— Select Editor —</option>
                @foreach($editors as $e)
                  <option value="{{ $e->id }}" {{ old('editor_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Publisher">
              <x-ui.input type="text" name="publisher" :value="old('publisher')" placeholder="Institution / Publisher name"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Subject Area">
              <x-ui.input type="text" name="subject_area" :value="old('subject_area')" placeholder="Computer Science, Engineering..."/>
            </x-ui.form-field>
          </div>
        </div>
        <x-ui.form-field label="Journal Description">
          <x-ui.textarea name="description" rows="3" placeholder="Brief description of the journal's scope and aims...">{{ old('description') }}</x-ui.textarea>
        </x-ui.form-field>
      </div>
    </div>

    <div style="display:flex;gap:12px;">
      <button type="submit" class="ds-btn ds-btn-pri">
        <i class="bi bi-check-lg"></i> Create Journal
      </button>
      <a href="{{ route('admin.journals.index') }}" class="ds-btn ds-btn-out">Cancel</a>
    </div>
  </form>
</div>

@endsection
