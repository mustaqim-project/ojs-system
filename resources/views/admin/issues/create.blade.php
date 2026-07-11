{{-- admin/issues/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><div class="pg-crumb"><a href="{{ route('admin.issues.index') }}">Issue</a><span>›</span><span class="cur">Tambah</span></div><h2 class="pg-title">Tambah Issue Baru</h2></div></div>
<div style="max-width:540px;">
  <div class="f-section fu"><div class="f-section-hdr"><h3 class="f-section-title">Informasi Issue</h3></div>
    <div class="f-section-body">
      <form method="POST" action="{{ route('admin.issues.store') }}">
        @csrf
        <div class="f-group"><label class="lbl">Jurnal <span class="req">*</span></label>
          <select name="journal_id" class="sel {{ $errors->has('journal_id')?'is-invalid':'' }}" required>
            <option value="">-- Pilih Jurnal --</option>
            @foreach($journals as $j)<option value="{{ $j->id }}" {{ old('journal_id')==$j->id?'selected':'' }}>{{ $j->title }}</option>@endforeach
          </select>@error('journal_id')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="f-group"><label class="lbl">Judul Issue <span class="req">*</span></label>
          <input class="inp" type="text" name="title" value="{{ old('title') }}" placeholder="Vol. 1 No. 1, Januari-Maret 2025" required/>
          @error('title')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="row g-3">
          <div class="col-4 f-group"><label class="lbl">Volume <span class="req">*</span></label><input class="inp" type="number" name="volume" value="{{ old('volume',1) }}" min="1" required/></div>
          <div class="col-4 f-group"><label class="lbl">Nomor <span class="req">*</span></label><input class="inp" type="number" name="number" value="{{ old('number',1) }}" min="1" required/></div>
          <div class="col-4 f-group"><label class="lbl">Tahun <span class="req">*</span></label><input class="inp" type="number" name="year" value="{{ old('year',date('Y')) }}" min="2000" max="2100" required/></div>
        </div>
        <div class="f-group mb-0"><label class="lbl">Status</label>
          <select name="status" class="sel">
            <option value="draft" {{ old('status')==='draft'?'selected':'' }}>Draft</option>
            <option value="published" {{ old('status')==='published'?'selected':'' }}>Published</option>
          </select></div>
        <div style="margin-top:20px;display:flex;gap:10px;">
          <button type="submit" class="btn-o btn-pri"><i class="bi bi-check-lg"></i> Simpan Issue</button>
          <a href="{{ route('admin.issues.index') }}" class="btn-o btn-out">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
