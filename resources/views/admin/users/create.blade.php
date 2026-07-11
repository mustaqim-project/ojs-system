{{-- admin/users/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div><div class="pg-crumb"><a href="{{ route('admin.users.index') }}">User</a><span>›</span><span class="cur">Tambah Baru</span></div>
  <h2 class="pg-title">Tambah User Baru</h2></div>
</div>
<div style="max-width:580px;">
  <div class="f-section fu">
    <div class="f-section-hdr"><h3 class="f-section-title">Informasi User</h3></div>
    <div class="f-section-body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="f-group"><label class="lbl">Nama Lengkap <span class="req">*</span></label>
          <input class="inp {{ $errors->has('name')?'is-invalid':'' }}" type="text" name="name" value="{{ old('name') }}" required/>
          @error('name')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="f-group"><label class="lbl">Email <span class="req">*</span></label>
          <input class="inp {{ $errors->has('email')?'is-invalid':'' }}" type="email" name="email" value="{{ old('email') }}" required/>
          @error('email')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="row g-3">
          <div class="col-md-6 f-group"><label class="lbl">Password <span class="req">*</span></label>
            <input class="inp" type="password" name="password" placeholder="Min. 8 karakter" required/>
            @error('password')<div class="f-err">{{ $message }}</div>@enderror</div>
          <div class="col-md-6 f-group"><label class="lbl">Role <span class="req">*</span></label>
            <select class="sel" name="role" required>
              @foreach($roles as $r)<option value="{{ $r }}" {{ old('role')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>@endforeach
            </select></div>
        </div>
        <div class="row g-3">
          <div class="col-md-8 f-group mb-0"><label class="lbl">Institusi</label>
            <input class="inp" type="text" name="affiliation" value="{{ old('affiliation') }}" placeholder="Universitas / Lembaga"/></div>
          <div class="col-md-4 f-group mb-0"><label class="lbl">Telepon</label>
            <input class="inp" type="text" name="phone" value="{{ old('phone') }}"/></div>
        </div>
        <div style="margin-top:20px;display:flex;gap:10px;">
          <button type="submit" class="btn-o btn-pri"><i class="bi bi-person-plus"></i> Simpan User</button>
          <a href="{{ route('admin.users.index') }}" class="btn-o btn-out">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
