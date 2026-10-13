@extends('layouts.app')
@section('title', 'Buat Pesanan - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Buat Pesanan Baru</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($pakets->isEmpty())
            <div class="alert alert-warning">
                Belum ada paket foto yang tersedia. Silakan hubungi fotografer.
            </div>
        @else
            <form method="POST" action="{{ route('pesanan.store') }}">
                @csrf

                <div class="form-group">
                    <label for="paket_id">Pilih Paket Foto</label>
                    <select name="paket_id" id="paket_id" class="form-control" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }} — Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Sesi Foto</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                           value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control"
                           value="{{ old('lokasi') }}" placeholder="Contoh: Taman Mini Indonesia Indah, Jakarta" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent">Kirim Pesanan</button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-dark">Batal</a>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
