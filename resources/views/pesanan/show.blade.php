@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $pesanan->id . ' - JepretYuk')

@php
    $isAdmin = auth()->user()->role === 'fotografer';
@endphp

@section('content')
<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="margin-bottom: 4px;">Detail Pesanan #{{ $pesanan->id }}</h1>
            <span style="color: #64748b; font-size: 0.9rem;">Dibuat pada {{ $pesanan->created_at->format('d M Y, H:i') }} WIB</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ $isAdmin ? route('fotografer.pesanan.index') : route('pesanan.index') }}" class="btn btn-outline-dark">← Kembali ke Daftar</a>
            @if($isAdmin)
                @if($pesanan->status !== 'selesai')
                    <a href="{{ route('fotografer.pesanan.edit', $pesanan) }}" class="btn btn-primary">Ubah Status / Catatan</a>
                @endif
            @else
                @if($pesanan->status === 'menunggu')
                    <a href="{{ route('pesanan.edit', $pesanan) }}" class="btn btn-primary">Edit Pesanan</a>
                @endif
            @endif
        </div>
    </div>

    {{-- Kartu Ringkasan Status & Paket --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        {{-- Kolom Kiri: Rincian Pesanan --}}
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <h2 style="font-size: 1.15rem; margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    Informasi Sesi Foto
                </h2>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                    <div style="color: #64748b; font-weight: 500;">Status Pesanan:</div>
                    <div>
                        <span class="badge badge-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span>
                        @if($pesanan->status === 'selesai')
                            <small style="color: #059669; font-weight: 600; margin-left: 8px;">(Sesi foto telah selesai dan terkunci)</small>
                        @endif
                    </div>
                </div>

                @if($isAdmin)
                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                        <div style="color: #64748b; font-weight: 500;">Nama Klien:</div>
                        <div><strong>{{ $pesanan->klien->name ?? '-' }}</strong> ({{ $pesanan->klien->email ?? '-' }})</div>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                    <div style="color: #64748b; font-weight: 500;">Tanggal Sesi:</div>
                    <div><strong>{{ \Carbon\Carbon::parse($pesanan->tanggal)->translatedFormat('l, d F Y') }}</strong></div>
                </div>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                    <div style="color: #64748b; font-weight: 500;">Lokasi Foto:</div>
                    <div>{{ $pesanan->lokasi }}</div>
                </div>

                {{-- Catatan Tambahan Klien --}}
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                    <div style="color: #64748b; font-weight: 500;">Catatan Klien:</div>
                    <div>
                        @if($pesanan->catatan_klien)
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; font-size: 0.9rem;">
                                {{ $pesanan->catatan_klien }}
                            </div>
                        @else
                            <span style="color: #94a3b8;">Tidak ada catatan khusus.</span>
                        @endif
                    </div>
                </div>

                {{-- Pesan / Catatan dari Fotografer --}}
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-top: 16px;">
                    <div style="color: #64748b; font-weight: 500;">Catatan Studio:</div>
                    <div>
                        @if($pesanan->catatan_fotografer)
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 6px; padding: 12px 14px; font-size: 0.92rem; color: #166534;">
                                <strong>📸 Pesan Fotografer:</strong>
                                <p style="margin: 6px 0 0 0;">{{ $pesanan->catatan_fotografer }}</p>
                            </div>
                        @else
                            <span style="color: #94a3b8;">Belum ada catatan dari fotografer.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bukti Pembayaran Card --}}
            <div class="card">
                <h2 style="font-size: 1.15rem; margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    Bukti Pembayaran / DP Transfer
                </h2>

                @if($pesanan->bukti_pembayaran)
                    <div style="margin-bottom: 16px;">
                        <img src="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                    </div>
                    <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                        Buka Gambar Ukuran Asli
                    </a>
                @else
                    <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                        Belum ada bukti pembayaran yang diunggah.
                    </div>
                @endif

                @if(!$isAdmin && $pesanan->status === 'menunggu')
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed #e2e8f0;">
                        <h4 style="margin: 0 0 8px 0; font-size: 0.95rem;">Unggah Bukti Transfer Baru</h4>
                        <form method="POST" action="{{ route('pesanan.uploadBukti', $pesanan) }}" enctype="multipart/form-data" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                            @csrf
                            <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required style="flex: 1; min-width: 200px;">
                            <button type="submit" class="btn btn-accent">Unggah Bukti</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Rincian Paket Foto Terpilih --}}
        <div>
            <div class="card">
                <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 12px; color: #1e293b;">
                    Paket Foto Pilihan
                </h3>

                @if($pesanan->paketFoto->foto)
                    <img src="{{ asset('storage/' . $pesanan->paketFoto->foto) }}" alt="{{ $pesanan->paketFoto->nama_paket }}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                @endif

                <h4 style="margin: 0 0 6px 0; font-size: 1.05rem; color: #1e293b;">
                    {{ $pesanan->paketFoto->nama_paket ?? '-' }}
                </h4>

                <div style="font-size: 1.3rem; font-weight: 800; color: #d4a853; margin-bottom: 12px;">
                    Rp {{ number_format($pesanan->paketFoto->harga ?? 0, 0, ',', '.') }}
                </div>

                <p style="color: #64748b; font-size: 0.88rem; line-height: 1.5; margin: 0;">
                    {{ $pesanan->paketFoto->deskripsi ?? 'Tidak ada rincian deskripsi paket.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
