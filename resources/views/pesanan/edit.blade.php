@extends('layouts.app')
@section('title', 'Edit Pesanan - JepretYuk')

@php
    $isAdmin = auth()->user()->role === 'fotografer';
@endphp

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{ $isAdmin ? 'Ubah Status Pesanan' : 'Edit Pesanan' }}</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Info pesanan (read-only) --}}
        <div class="alert alert-info" style="margin-bottom: 24px;">
            <strong>Pesanan #{{ $pesanan->id }}</strong>
            @if($isAdmin)
                — oleh {{ $pesanan->klien->name }}
            @endif
            <br>
            Paket: {{ $pesanan->paketFoto->nama_paket }} |
            Tanggal: {{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }} |
            Lokasi: {{ $pesanan->lokasi }}
        </div>

        @if($isAdmin)
            {{-- Fotografer: hanya ubah status --}}
            <form method="POST" action="{{ route('fotografer.pesanan.update', $pesanan) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="status">Status Pesanan</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="menunggu" {{ old('status', $pesanan->status) === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ old('status', $pesanan->status) === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="selesai" {{ old('status', $pesanan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent">Perbarui Status</button>
                    <a href="{{ route('fotografer.pesanan.index') }}" class="btn btn-outline-dark">Kembali</a>
                </div>
            </form>
        @else
            {{-- Klien: ubah detail pesanan --}}
            <form method="POST" action="{{ route('pesanan.update', $pesanan) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="paket_id">Pilih Paket Foto</label>
                    <select name="paket_id" id="paket_id" class="form-control" required>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}" {{ old('paket_id', $pesanan->paket_id) == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }} — Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Sesi Foto</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                           value="{{ old('tanggal', $pesanan->tanggal) }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control"
                           value="{{ old('lokasi', $pesanan->lokasi) }}" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent">Perbarui Pesanan</button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-dark">Batal</a>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
