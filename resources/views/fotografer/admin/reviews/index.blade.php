@extends('layouts.app')
@section('title', 'Ulasan & Rating Klien - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="margin-bottom: 4px;">Ulasan & Rating Klien</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Rekap penilaian dan ulasan dari klien yang telah menggunakan jasa Anda.</p>
        </div>
        <a href="{{ route('fotografer.pesanan.index') }}" class="btn btn-outline-dark">← Kembali ke Daftar Pesanan</a>
    </div>

    {{-- Kotak Rata-rata Rating Keseluruhan --}}
    <div class="card mb-4" style="background: #f8fafc; padding: 24px; border-radius: 8px; text-align: center; margin-bottom: 24px; border: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Rata-rata Rating Keseluruhan</h3>
        <div style="font-size: 2.8rem; font-weight: 800; color: #d4a853; margin: 8px 0;">
            ⭐ {{ number_format($averageRating ?? 0, 1) }} <span style="font-size: 1.2rem; color: #64748b; font-weight: normal;">/ 5.0</span>
        </div>
        <p style="margin: 0; font-size: 0.88rem; color: #64748b;">Berdasarkan total semua ulasan yang masuk dari klien.</p>
    </div>

    {{-- Daftar Ulasan --}}
    @forelse($reviews as $item)
        <div class="card" style="margin-bottom: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h5 style="margin: 0; font-size: 1.05rem; color: #1e293b;">
                        <strong>{{ $item->user->name ?? 'Klien' }}</strong> 
                        <span style="font-weight: normal; font-size: 0.85rem; color: #64748b; margin-left: 6px;">(Pesanan #{{ $item->pesanan_id }})</span>
                    </h5>
                    <span style="color: #64748b; font-size: 0.85rem;">{{ $item->created_at->diffForHumans() }}</span>
                </div>

                <div style="color: #d4a853; font-size: 1rem; margin-bottom: 8px;">
                    @for ($i = 0; $i < $item->rating; $i++) ⭐ @endfor
                    <strong style="color: #334155; margin-left: 6px;">({{ $item->rating }}/5)</strong>
                </div>

                <p style="margin: 0; color: #475569; font-size: 0.95rem; line-height: 1.5;">
                    "{{ $item->review ?? 'Klien memberikan rating tanpa menuliskan komentar teks.' }}"
                </p>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; padding: 40px; color: #64748b;">
            <p style="margin: 0; font-size: 0.95rem;">Belum ada ulasan atau rating yang masuk dari klien.</p>
        </div>
    @endforelse

    {{-- Navigasi Pagination --}}
    <div style="margin-top: 24px;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection