@extends('layouts.app')
@section('title', 'Daftar Pesanan - JepretYuk')

@php
    $isAdmin = auth()->user()->role === 'fotografer';
@endphp

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{ $isAdmin ? 'Semua Pesanan' : 'Pesanan Saya' }}</h1>
        @if(!$isAdmin)
            <a href="{{ route('pesanan.create') }}" class="btn btn-accent">+ Buat Pesanan</a>
        @endif
    </div>

    @if($pesanans->isEmpty())
        <div class="card">
            <div class="table-empty">
                @if($isAdmin)
                    Belum ada pesanan masuk dari klien.
                @else
                    Anda belum memiliki pesanan. Klik "Buat Pesanan" untuk memesan fotografer.
                @endif
            </div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        @if($isAdmin)
                            <th>Klien</th>
                        @endif
                        <th>Paket</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanans as $index => $pesanan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($isAdmin)
                                <td>{{ $pesanan->klien->name }}</td>
                            @endif
                            <td>{{ $pesanan->paketFoto->nama_paket }}</td>
                            <td>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}</td>
                            <td>{{ $pesanan->lokasi }}</td>
                            <td>
                                <span class="badge badge-{{ $pesanan->status }}">{{ $pesanan->status }}</span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    @if($isAdmin)
                                        <a href="{{ route('fotografer.pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Ubah Status</a>
                                        <form action="{{ route('fotografer.pesanan.destroy', $pesanan) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @else
                                        @if($pesanan->status === 'menunggu')
                                            <a href="{{ route('pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('pesanan.destroy', $pesanan) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        @else
                                            <span class="form-hint">Tidak bisa diedit</span>
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
