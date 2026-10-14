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
            {{-- Fotografer: ubah status & beri catatan/alasan --}}
            <form method="POST" action="{{ route('fotografer.pesanan.update', $pesanan->id) }}"
                  onsubmit="
                    const st = document.getElementById('status').value;
                    if (st === 'selesai') {
                        return confirm('Pesanan dengan status selesai tidak dapat diubah kembali. Lanjutkan?');
                    } else if (st === 'ditolak') {
                        return confirm('Yakin ingin menolak pesanan ini?');
                    }
                    return true;
                  ">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="status">Status Pesanan</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="menunggu" {{ old('status', $pesanan->status) === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ old('status', $pesanan->status) === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ old('status', $pesanan->status) === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="selesai" {{ old('status', $pesanan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="catatan_fotografer">Catatan untuk Klien</label>
                    <textarea name="catatan_fotografer" id="catatan_fotografer" class="form-control" rows="3"
                              placeholder="Catatan atau keterangan (opsional)">{{ old('catatan_fotografer', $pesanan->catatan_fotografer) }}</textarea>
                </div>

                @if($pesanan->bukti_pembayaran)
                    <div class="form-group">
                        <label>Bukti Pembayaran</label>
                        <div>
                            <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-dark">Lihat Bukti Transfer</a>
                        </div>
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent">Perbarui Status</button>
                    <a href="{{ route('fotografer.pesanan.index') }}" class="btn btn-outline-dark">Kembali</a>
                </div>
            </form>
        @else
            {{-- Klien: ubah detail pesanan --}}
            <form method="POST" action="{{ route('pesanan.update', $pesanan) }}" enctype="multipart/form-data">
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

                <div class="form-group">
                    <label for="catatan_klien">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan_klien" id="catatan_klien" class="form-control" rows="3">{{ old('catatan_klien', $pesanan->catatan_klien) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="bukti_pembayaran">Unggah / Ganti Bukti Pembayaran</label>
                    @if($pesanan->bukti_pembayaran)
                        <div style="margin-bottom: 8px;">
                            <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank" style="font-size: 13px; color: #2563eb;">Lihat Bukti yang Sudah Diunggah</a>
                        </div>
                    @endif
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" accept="image/*">
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
