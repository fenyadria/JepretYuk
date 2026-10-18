@extends('layouts.app')
@section('title', 'Dashboard Fotografer - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Dashboard Fotografer</h1>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('paket.create') }}" class="btn btn-accent">+ Tambah Paket</a>
            <a href="{{ route('fotografer.pesanan.index') }}" class="btn btn-primary">Semua Pesanan</a>
        </div>
    </div>

    {{-- Ringkasan Statistik & Omset --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 26px; font-weight: 800; color: #059669;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Estimasi Omset</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #d97706;">{{ $pesananMenunggu }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Perlu Konfirmasi</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #2563eb;">{{ $pesananDisetujui }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Jadwal Aktif</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">{{ $totalPesanan }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Total Pesanan</div>
        </div>
    </div>

    {{-- Ringkasan Tambahan (kartu bisa diklik) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('fotografer.pesanan.index', ['status' => 'selesai']) }}" class="card" style="text-align: center; padding: 20px; text-decoration: none;">
            <div style="font-size: 28px; font-weight: 800; color: #059669;">{{ $pesananSelesai }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Pesanan Selesai</div>
        </a>
        <a href="{{ route('fotografer.pesanan.index', ['status' => 'ditolak']) }}" class="card" style="text-align: center; padding: 20px; text-decoration: none;">
            <div style="font-size: 28px; font-weight: 800; color: #dc2626;">{{ $pesananDitolak }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Pesanan Ditolak</div>
        </a>
        <div class="card" style="text-align: center; padding: 20px;">
            <div style="font-size: 28px; font-weight: 800; color: #7c3aed;">{{ $totalKlien }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Total Klien</div>
        </div>
        <a href="{{ route('paket.index') }}" class="card" style="text-align: center; padding: 20px; text-decoration: none;">
            <div style="font-size: 28px; font-weight: 800; color: #0891b2;">{{ $totalPaket }}</div>
            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">Total Paket Foto</div>
        </a>
    </div>

    {{-- Pesanan Baru yang Butuh Konfirmasi --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 18px; margin: 0;">Pesanan Menunggu Konfirmasi</h2>
            <a href="{{ route('fotografer.pesanan.index') }}?status=menunggu" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
        </div>

        @if($pesananTerbaru->isEmpty())
            <div class="table-empty">
                Tidak ada pesanan yang menunggu konfirmasi saat ini.
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Klien</th>
                            <th>Paket</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesananTerbaru as $pesanan)
                            <tr>
                                <td>{{ $pesanan->klien->name ?? '-' }}</td>
                                <td>{{ $pesanan->paketFoto->nama_paket ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}</td>
                                <td>{{ $pesanan->lokasi }}</td>
                                <td>
                                    <a href="{{ route('fotografer.pesanan.edit', $pesanan) }}" class="btn btn-sm btn-primary">Tinjau</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Jadwal Sesi Foto Terdekat --}}
    <div class="card" style="margin-bottom: 24px;">
        <h2 style="font-size: 18px; margin: 0 0 16px;">📅 Jadwal Sesi Foto Terdekat</h2>

        @if($jadwalTerdekat->isEmpty())
            <div class="table-empty">Belum ada jadwal sesi foto yang disetujui.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Klien</th>
                            <th>Paket</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalTerdekat as $jadwal)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</strong></td>
                                <td>{{ $jadwal->klien->name ?? '-' }}</td>
                                <td>{{ $jadwal->paketFoto->nama_paket ?? '-' }}</td>
                                <td>{{ $jadwal->lokasi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Paket Terlaris --}}
    <div class="card" style="margin-bottom: 24px;">
        <h2 style="font-size: 18px; margin: 0 0 16px;">🏆 Paket Terlaris</h2>

        @if($paketTerlaris->isEmpty())
            <div class="table-empty">Belum ada data paket.</div>
        @else
            @foreach($paketTerlaris as $paket)
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
                    <span><strong>{{ $loop->iteration }}.</strong> {{ $paket->nama_paket }}</span>
                    <span style="color: #64748b;">{{ $paket->pesanans_count }} pesanan</span>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection