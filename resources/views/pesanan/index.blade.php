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
                        <th style="text-align: center; width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanans as $index => $pesanan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($isAdmin)
                                <td>
                                    <strong>{{ $pesanan->klien->name ?? '-' }}</strong>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $pesanan->klien->email ?? '' }}</div>
                                </td>
                            @endif
                            <td>
                                <strong>{{ $pesanan->paketFoto->nama_paket ?? '-' }}</strong>
                                <div style="font-size: 0.82rem; color: var(--accent); font-weight: 600;">
                                    Rp {{ number_format($pesanan->paketFoto->harga ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}
                            </td>
                            <td>{{ $pesanan->lokasi }}</td>
                            <td>
                                <span class="badge badge-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="table-actions" style="justify-content: center;">
                                    <a href="{{ $isAdmin ? route('fotografer.pesanan.show', $pesanan) : route('pesanan.show', $pesanan) }}" class="btn btn-sm btn-outline-dark">
                                        Detail
                                    </a>

                                    @if($isAdmin)
                                        @if($pesanan->status !== 'selesai')
                                            <a href="{{ route('fotografer.pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Ubah Status</a>
                                        @endif
                                        <form action="{{ route('fotografer.pesanan.destroy', $pesanan) }}" method="POST"
                                              onsubmit="return confirm('Hapus data pesanan ini?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @else
                                        @if($pesanan->status === 'menunggu')
                                            <a href="{{ route('pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('pesanan.destroy', $pesanan) }}" method="POST"
                                                  onsubmit="return confirm('Batalkan pesanan ini?')" style="display:inline;">
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
