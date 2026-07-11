{{-- admin/journals/edit.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><div class="pg-crumb"><a href="{{ route('admin.journals.index') }}">Jurnal</a><span>›</span><span class="cur">Edit</span></div><h2 class="pg-title">Edit: {{ $journal->abbreviation ?? $journal->title }}</h2></div></div>
<div style="max-width:680px;">
  <form method="POST" action="{{ route('admin.journals.update',$journal) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="f-section fu"><div class="f-section-hdr"><h3 class="f-section-title">Informasi Jurnal</h3></div>
      <div class="f-section-body">
        <div class="f-group"><label class="lbl">Nama Jurnal <span class="req">*</span></label>
          <input class="inp" type="text" name="title" value="{{ old('title',$journal->title) }}" required/></div>
        <div class="row g-3">
          <div class="col-md-6 f-group"><label class="lbl">Frekuensi <span class="req">*</span></label>
            <select name="frequency" class="sel">
              @foreach(['monthly'=>'Bulanan','bimonthly'=>'2 Bulan','quarterly'=>'3 Bulan','semiannual'=>'6 Bulan','annual'=>'Tahunan'] as $v=>$l)
              <option value="{{ $v }}" {{ old('frequency',$journal->frequency)===$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select></div>
          <div class="col-md-6 f-group"><label class="lbl">Editor</label>
            <select name="editor_id" class="sel">
              <option value="">-- Tidak ada --</option>
              @foreach($editors as $e)<option value="{{ $e->id }}" {{ old('editor_id',$journal->editor_id)==$e->id?'selected':'' }}>{{ $e->name }}</option>@endforeach
            </select></div>
        </div>
        <div class="f-group"><label class="lbl">Deskripsi</label>
          <textarea name="description" class="txta" rows="3">{{ old('description',$journal->description) }}</textarea></div>
        <div class="f-group mb-0" style="display:flex;align-items:center;gap:10px;">
          <input type="hidden" name="is_active" value="0"/>
          <input type="checkbox" id="ia" name="is_active" value="1" {{ old('is_active',$journal->is_active)?'checked':'' }} style="width:16px;height:16px;accent-color:var(--acc);cursor:pointer;"/>
          <label for="ia" style="font-size:13px;font-weight:600;cursor:pointer;">Jurnal Aktif</label>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 fu fd2">
      <button type="submit" class="btn-o btn-pri"><i class="bi bi-check-lg"></i> Update Jurnal</button>
      <a href="{{ route('admin.journals.index') }}" class="btn-o btn-out">Batal</a>
    </div>
  </form>
</div>
@endsection
