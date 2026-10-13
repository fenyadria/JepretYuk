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
                <label class="form-file-label" for="foto" style="cursor: pointer; display: block; border: 2px dashed #ccc; padding: 15px; border-radius: 6px; text-align: center;">
                    <span>📎</span>
                    <span id="file-label-text">Pilih foto baru (opsional)</span>
                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" style="display: none;" onchange="updateFileName(this)">
                </label>

                <div style="margin-top: 10px;">
                    <img id="image-preview" src="#" alt="Preview Foto Baru" style="display: none; max-width: 150px; border-radius: 6px;" />
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">Perbarui Paket</button>
                <a href="{{ route('paket.index') }}" class="btn btn-outline-dark">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileName(input) {
        const textSpan = document.getElementById('file-label-text');
        const preview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            textSpan.textContent = "File terpilih: " + input.files[0].name;

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            textSpan.textContent = 'Pilih foto baru (opsional)';
            preview.style.display = 'none';
        }
    }
</script>
@endsection
