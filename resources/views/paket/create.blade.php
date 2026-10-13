@extends('layouts.app')
@section('title', 'Tambah Paket Foto - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Tambah Paket Foto</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('paket.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama_paket">Nama Paket</label>
                <input type="text" name="nama_paket" id="nama_paket" class="form-control"
                       value="{{ old('nama_paket') }}" placeholder="Contoh: Paket Prewedding Gold" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control"
                          placeholder="Jelaskan apa saja yang termasuk dalam paket ini" required>{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" class="form-control"
                       value="{{ old('harga') }}" placeholder="500000" min="0" required>
            </div>

            <div class="form-group">
                <label>Foto Paket</label>
                <label class="form-file-label">
                    <span>📎</span>
                    <span>Pilih foto (JPEG/PNG, maks 2MB)</span>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg">
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">Simpan Paket</button>
                <a href="{{ route('paket.index') }}" class="btn btn-outline-dark">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
