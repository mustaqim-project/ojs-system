{{-- admin/journals/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><div class="pg-crumb"><a href="{{ route('admin.journals.index') }}">Jurnal</a><span>›</span><span class="cur">Tambah</span></div><h2 class="pg-title">Tambah Jurnal Baru</h2></div></div>
<div style="max-width:680px;">
  <form method="POST" action="{{ route('admin.journals.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="f-section fu"><div class="f-section-hdr"><h3 class="f-section-title">Informasi Jurnal</h3></div>
      <div class="f-section-body">
        <div class="f-group"><label class="lbl">Nama Jurnal <span class="req">*</span></label>
          <input class="inp {{ $errors->has('title')?'is-invalid':'' }}" type="text" name="title" value="{{ old('title') }}" required/>
          @error('title')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="row g-3">
          <div class="col-md-4 f-group"><label class="lbl">Singkatan (ABBR)</label><input class="inp" type="text" name="abbreviation" value="{{ old('abbreviation') }}" placeholder="JIKTI"/></div>
          <div class="col-md-4 f-group"><label class="lbl">ISSN Print</label><input class="inp" type="text" name="issn_print" value="{{ old('issn_print') }}" placeholder="XXXX-XXXX"/></div>
          <div class="col-md-4 f-group"><label class="lbl">ISSN Online</label><input class="inp" type="text" name="issn_online" value="{{ old('issn_online') }}" placeholder="XXXX-XXXX"/></div>
        </div>
        <div class="row g-3">
          <div class="col-md-6 f-group"><label class="lbl">Frekuensi <span class="req">*</span></label>
            <select name="frequency" class="sel" required>
              @foreach(['monthly'=>'Bulanan (Monthly)','bimonthly'=>'2 Bulan (Bimonthly)','quarterly'=>'3 Bulan (Quarterly)','semiannual'=>'6 Bulan (Semiannual)','annual'=>'Tahunan (Annual)'] as $v=>$l)
              <option value="{{ $v }}" {{ old('frequency')===$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select></div>
          <div class="col-md-6 f-group"><label class="lbl">Editor</label>
            <select name="editor_id" class="sel">
              <option value="">-- Pilih Editor --</option>
              @foreach($editors as $e)<option value="{{ $e->id }}" {{ old('editor_id')==$e->id?'selected':'' }}>{{ $e->name }}</option>@endforeach
            </select></div>
        </div>
        <div class="row g-3">
          <div class="col-md-6 f-group"><label class="lbl">Penerbit</label><input class="inp" type="text" name="publisher" value="{{ old('publisher') }}"/></div>
          <div class="col-md-6 f-group"><label class="lbl">Bidang Ilmu</label><input class="inp" type="text" name="subject_area" value="{{ old('subject_area') }}"/></div>
        </div>
        <div class="f-group mb-0"><label class="lbl">Deskripsi</label>
          <textarea name="description" class="txta" rows="3">{{ old('description') }}</textarea></div>
      </div>
    </div>
    <div class="d-flex gap-2 fu fd2">
      <button type="submit" class="btn-o btn-pri"><i class="bi bi-check-lg"></i> Simpan Jurnal</button>
      <a href="{{ route('admin.journals.index') }}" class="btn-o btn-out">Batal</a>
    </div>
  </form>
</div>
@endsection
