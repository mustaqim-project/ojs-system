{{-- admin/users/edit.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div><div class="pg-crumb"><a href="{{ route('admin.users.index') }}">User</a><span>›</span><span class="cur">Edit</span></div>
  <h2 class="pg-title">Edit: {{ $user->name }}</h2></div>
</div>
<div style="max-width:580px;">
  <div class="f-section fu">
    <div class="f-section-hdr"><h3 class="f-section-title">Data User</h3></div>
    <div class="f-section-body">
      <form method="POST" action="{{ route('admin.users.update',$user) }}">
        @csrf @method('PUT')
        <div class="f-group"><label class="lbl">Nama <span class="req">*</span></label>
          <input class="inp {{ $errors->has('name')?'is-invalid':'' }}" type="text" name="name" value="{{ old('name',$user->name) }}" required/>
          @error('name')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="f-group"><label class="lbl">Email <span class="req">*</span></label>
          <input class="inp {{ $errors->has('email')?'is-invalid':'' }}" type="email" name="email" value="{{ old('email',$user->email) }}" required/>
          @error('email')<div class="f-err">{{ $message }}</div>@enderror</div>
        <div class="row g-3">
          <div class="col-md-6 f-group"><label class="lbl">Password Baru <span class="hint">(kosong = tidak ubah)</span></label>
            <input class="inp" type="password" name="password"/></div>
          <div class="col-md-6 f-group"><label class="lbl">Role <span class="req">*</span></label>
            <select class="sel" name="role" required>
              @foreach($roles as $r)<option value="{{ $r }}" {{ old('role',$user->role)===$r?'selected':'' }}>{{ ucfirst($r) }}</option>@endforeach
            </select></div>
        </div>
        <div class="f-group"><label class="lbl">Institusi</label>
          <input class="inp" type="text" name="affiliation" value="{{ old('affiliation',$user->affiliation) }}"/></div>
        <div class="f-group mb-0" style="display:flex;align-items:center;gap:10px;">
          <input type="hidden" name="is_active" value="0"/>
          <input type="checkbox" id="ia" name="is_active" value="1" {{ old('is_active',$user->is_active)?'checked':'' }} style="width:16px;height:16px;accent-color:var(--acc);cursor:pointer;"/>
          <label for="ia" style="font-size:13px;font-weight:600;cursor:pointer;">Akun Aktif</label>
        </div>
        <div style="margin-top:20px;display:flex;gap:10px;">
          <button type="submit" class="btn-o btn-pri"><i class="bi bi-check-lg"></i> Update User</button>
          <a href="{{ route('admin.users.index') }}" class="btn-o btn-out">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
