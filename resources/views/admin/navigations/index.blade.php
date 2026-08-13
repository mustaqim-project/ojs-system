@extends('layouts.admin')
@section('header', 'Kelola Navigasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Navigasi Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('navigations.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Judul</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" required placeholder="/about atau #">
                        </div>
                        <div class="mb-3">
                            <label>Induk Menu (Opsional)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">Tidak Ada Induk (Root)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Urutan</label>
                            <input type="number" name="order" class="form-control" value="0">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Navigasi</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Struktur Navigasi</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($navigations as $nav)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $nav->title }}</strong> ({{ $nav->url }}) - Urutan: {{ $nav->order }}
                                </div>
                                <form action="{{ route('navigations.destroy', $nav->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus menu ini?')">Hapus</button>
                                </form>
                            </li>
                            @if($nav->children->count() > 0)
                                <ul class="list-group ms-4 mt-2 mb-2">
                                    @foreach($nav->children as $child)
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                            <div>
                                                {{ $child->title }} ({{ $child->url }}) - Urutan: {{ $child->order }}
                                            </div>
                                            <form action="{{ route('navigations.destroy', $child->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">Hapus</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
