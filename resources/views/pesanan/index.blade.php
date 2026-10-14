@extends('layouts.app')
@section('title', 'Daftar Pesanan - JepretYuk')

@php
    $isAdmin = auth()->user()->role === 'fotografer';
@endphp

@section('content')
<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h1>{{ $isAdmin ? 'Semua Pesanan Masuk' : 'Pesanan Saya' }}</h1>
        @if(!$isAdmin)
            <a href="{{ route('pesanan.create') }}" class="btn btn-accent">+ Buat Pesanan Baru</a>
        @endif
    </div>

    {{-- Filter & Pencarian --}}
    <div class="card" style="margin-bottom: 20px; padding: 16px;">
        <form method="GET" action="{{ $isAdmin ? route('fotografer.pesanan.index') : route('pesanan.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari {{ $isAdmin ? 'nama klien, paket, atau lokasi...' : 'paket atau lokasi...' }}">
            </div>
            <div style="min-width: 160px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['q', 'status', 'tanggal']))
                <a href="{{ $isAdmin ? route('fotografer.pesanan.index') : route('pesanan.index') }}" class="btn btn-outline-dark">Reset</a>
            @endif
        </form>
    </div>

    @if($pesanans->isEmpty())
        <div class="card">
            <div class="table-empty">
                @if($isAdmin)
                    Belum ada data pesanan yang cocok dengan filter pencarian.
                @else
                    Belum ada data pesanan. Klik "Buat Pesanan Baru" untuk mulai memesan jasa fotografi.
                @endif
            </div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        @if($isAdmin)
                            <th>Klien</th>
                        @endif
                        <th>Paket Foto</th>
                        <th>Tanggal Sesi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th style="min-width: 260px;">Catatan & Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanans as $index => $pesanan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($isAdmin)
                                <td>
                                    <strong>{{ $pesanan->klien->name ?? '-' }}</strong>
                                    <div style="font-size: 0.8rem; color: #64748b;">{{ $pesanan->klien->email ?? '' }}</div>
                                </td>
                            @endif
                            <td>
                                <strong>{{ $pesanan->paketFoto->nama_paket ?? '-' }}</strong>
                                <div style="font-size: 0.82rem; color: #d4a853; font-weight: 600;">
                                    Rp {{ number_format($pesanan->paketFoto->harga ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}</strong>
                            </td>
                            <td>{{ $pesanan->lokasi }}</td>
                            <td>
                                <span class="badge badge-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span>
                            </td>
                            <td>
                                {{-- Catatan dari Fotografer (Paling Penting untuk Klien!) --}}
                                @if($pesanan->catatan_fotografer)
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 3px solid #16a34a; border-radius: 6px; padding: 8px 10px; font-size: 0.85rem; color: #166534; margin-bottom: 6px;">
                                        <div style="font-weight: 700; display: flex; align-items: center; gap: 4px; margin-bottom: 2px;">
                                            📸 Catatan Fotografer:
                                        </div>
                                        <span>"{{ $pesanan->catatan_fotografer }}"</span>
                                    </div>
                                @endif

                                {{-- Catatan dari Klien --}}
                                @if($pesanan->catatan_klien)
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; font-size: 0.82rem; color: #475569; margin-bottom: 6px;">
                                        <strong>📝 Catatan Klien:</strong> {{ $pesanan->catatan_klien }}
                                    </div>
                                @endif

                                {{-- Bukti Pembayaran / Transfer --}}
                                @if($pesanan->bukti_pembayaran)
                                    <div>
                                        <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-dark" style="font-size: 0.78rem; padding: 3px 8px;">
                                            🧾 Lihat Bukti Transfer
                                        </a>
                                    </div>
                                @elseif(!$pesanan->catatan_fotografer && !$pesanan->catatan_klien)
                                    <span style="color: #94a3b8; font-size: 0.82rem;">Tidak ada catatan</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    {{-- Tombol Lihat Detail untuk Klien & Admin --}}
                                    <a href="{{ $isAdmin ? route('fotografer.pesanan.show', $pesanan) : route('pesanan.show', $pesanan) }}" class="btn btn-sm btn-outline-dark">
                                        Detail
                                    </a>

                                    @if($isAdmin)
                                        @if($pesanan->status === 'selesai')
                                            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.8rem; color: #059669; font-weight: 600; padding: 4px 8px; background: #ecfdf5; border-radius: 4px; border: 1px solid #a7f3d0;">
                                                🔒 Selesai
                                            </span>
                                        @else
                                            <a href="{{ route('fotografer.pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Ubah Status</a>
                                        @endif
                                        <form action="{{ route('fotografer.pesanan.destroy', $pesanan) }}" method="POST"
                                              onsubmit="return confirm('⚠️ PERINGATAN HAPUS DATA:\nApakah Anda yakin ingin menghapus pesanan #{{ $pesanan->id }} milik {{ $pesanan->klien->name ?? 'klien' }}?\n\nData yang dihapus tidak dapat dipulihkan!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @else
                                        @if($pesanan->status === 'menunggu')
                                            <a href="{{ route('pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('pesanan.destroy', $pesanan) }}" method="POST"
                                                  onsubmit="return confirm('⚠️ KONFIRMASI PEMBATALAN:\nApakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Batal</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
