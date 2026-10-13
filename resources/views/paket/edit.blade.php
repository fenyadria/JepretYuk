@extends('layouts.app')
@section('title', 'Edit Paket Foto - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Edit Paket Foto</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('paket.update', $paket) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_paket">Nama Paket</label>
                <input type="text" name="nama_paket" id="nama_paket" class="form-control"
                       value="{{ old('nama_paket', $paket->nama_paket) }}" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control"
                          required>{{ old('deskripsi', $paket->deskripsi) }}</textarea>
            </div>

            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" class="form-control"
                       value="{{ old('harga', $paket->harga) }}" min="0" required>
            </div>

            <div class="form-group">
                <label>Foto Paket</label>
                @if($paket->foto)
                    <div class="current-foto">
                        <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}">
                        <p class="form-hint">Foto saat ini. Upload foto baru untuk mengganti.</p>
                    </div>
                @endif
                <label class="form-file-label" style="margin-top: 8px;">
                    <span>📎</span>
                    <span>Pilih foto baru (opsional)</span>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg">
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">Perbarui Paket</button>
                <a href="{{ route('paket.index') }}" class="btn btn-outline-dark">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
