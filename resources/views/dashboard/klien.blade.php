@extends('layouts.app')
@section('title', 'Dashboard Klien - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Dashboard Klien</h1>
        <a href="{{ route('pesanan.create') }}" class="btn btn-accent">+ Buat Pesanan Baru</a>
    </div>

    {{-- Ringkasan Pesanan --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">{{ $totalPesananSaya }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Total Pesanan</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #d97706;">{{ $pesananMenunggu }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Menunggu Konfirmasi</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #059669;">{{ $pesananDisetujui }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Jadwal Disetujui</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #2563eb;">{{ $pesananSelesai }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Selesai</div>
        </div>
    </div>

    {{-- Pesanan Terkini --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 18px; margin: 0;">Pesanan Terkini</h2>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
        </div>

        @if($pesananAktif->isEmpty())
            <div class="table-empty">
                Belum ada pesanan aktif. Mulai pesan fotografer sekarang!
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Tanggal Sesi</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Catatan Fotografer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesananAktif as $pesanan)
                            <tr>
                                <td>{{ $pesanan->paketFoto->nama_paket ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}</td>
                                <td>{{ $pesanan->lokasi }}</td>
                                <td>
                                    <span class="badge badge-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span>
                                </td>
                                <td>
                                    @if($pesanan->catatan_fotografer)
                                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 8px; font-size: 0.82rem; color: #166534;">
                                            📸 {{ $pesanan->catatan_fotografer }}
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-size: 0.82rem;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
