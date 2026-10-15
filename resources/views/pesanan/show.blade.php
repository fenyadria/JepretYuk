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

    {{-- Kartu Ringkasan Status & Paket (Grid 2 Kolom) --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        {{-- ================= KOLSOM KIRI ================= --}}
        <div>
            {{-- Informasi Sesi Foto --}}
            <div class="card" style="margin-bottom: 24px;">
                <h2 style="font-size: 1.15rem; margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    Informasi Sesi Foto
                </h2>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-bottom: 12px;">
                    <div style="color: #64748b; font-weight: 500;">Status Pesanan:</div>
                    <div>
                        <span class="badge badge-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span>
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

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 12px; margin-top: 16px;">
                    <div style="color: #64748b; font-weight: 500;">Catatan Studio:</div>
                    <div>
                        @if($pesanan->catatan_fotografer)
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 6px; padding: 12px 14px; font-size: 0.92rem; color: #166534;">
                                <strong>Catatan Fotografer:</strong>
                                <p style="margin: 6px 0 0 0;">{{ $pesanan->catatan_fotografer }}</p>
                            </div>
                        @else
                            <span style="color: #94a3b8;">Belum ada catatan dari fotografer.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bukti Pembayaran Card --}}
            <div class="card" style="margin-bottom: 24px;">
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

                {{-- Informasi Rekening & Form Unggah Bukti untuk Klien --}}
                @if(!$isAdmin && $pesanan->status === 'menunggu')
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed #e2e8f0;">
                        
                        {{-- Kotak Informasi Rekening --}}
                        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 8px 0; font-size: 0.95rem; color: #1e293b;">
                                💳 Informasi Rekening Pembayaran
                            </h4>
                            <p style="font-size: 0.85rem; color: #475569; margin: 0 0 8px 0;">
                                Silakan melakukan transfer pembayaran / DP ke salah satu rekening berikut sebelum mengunggah bukti:
                            </p>
                            <ul style="margin: 0; padding-left: 20px; font-size: 0.88rem; color: #334155; line-height: 1.6;">
                                <li><strong>BCA:</strong> 1234-5678-90 (a.n. Studio JepretYuk)</li>
                                <li><strong>Mandiri:</strong> 0987-6543-21 (a.n. Studio JepretYuk)</li>
                                <li><strong>E-Wallet (Dana/OVO):</strong> 0812-3456-7890 (a.n. JepretYuk)</li>
                            </ul>
                        </div>

                        {{-- Form Unggah --}}
                        <h4 style="margin: 0 0 8px 0; font-size: 0.95rem;">Unggah Bukti Transfer Baru</h4>
                        <form method="POST" action="{{ route('pesanan.uploadBukti', $pesanan) }}" enctype="multipart/form-data" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                            @csrf
                            <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required style="flex: 1; min-width: 200px;">
                            <button type="submit" class="btn btn-accent">Unggah Bukti</button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Form Ulasan / Rating (Posisinya di dalam kolom kiri agar rapi) --}}
            @if(!$isAdmin)
                <div class="card">
                    <h2 style="font-size: 1.15rem; margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        Ulasan & Rating Pesanan
                    </h2>
                
                    @if($pesanan->status == 'selesai' && !$pesanan->review)
                        <form action="{{ route('user.reviews.store', $pesanan->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (1-5)</label>
                                <select name="rating" id="rating" class="form-control" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (Sempurna)</option>
                                    <option value="4">⭐⭐⭐⭐ (Bagus)</option>
                                    <option value="3">⭐⭐⭐ (Cukup)</option>
                                    <option value="2">⭐⭐ (Kurang)</option>
                                    <option value="1">⭐ (Buruk)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="review" class="form-label">Ulasan Anda</label>
                                <textarea name="review" id="review" class="form-control" rows="3" placeholder="Ceritakan pengalaman Anda..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                        </form>

                    @elseif($pesanan->review)
                        {{-- Tampilan Ulasan yang Telah Diberikan Beserta Tombol Edit --}}
                        <div id="view-review-box" style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <p class="text-success mb-1" style="color: #16a34a; font-weight: 600; margin-bottom: 6px;">Ulasan yang Telah Anda Berikan:</p>
                            <p class="text-warning mb-1" style="margin-bottom: 6px;">
                                @for ($i = 0; $i < $pesanan->review->rating; $i++) ⭐ @endfor
                                <strong style="color: #334155;">({{ $pesanan->review->rating }}/5)</strong>
                            </p>
                            <p class="mb-3 text-muted" style="color: #475569; margin-bottom: 16px; line-height: 1.5;">{{ $pesanan->review->review ?? 'Tanpa catatan teks.' }}</p>
                            
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.getElementById('view-review-box').style.display='none'; document.getElementById('edit-review-box').style.display='block';">
                                Edit Ulasan
                            </button>
                        </div>

                        {{-- Form Edit Ulasan (Default Disembunyikan, Muncul Saat Tombol Edit Ditekan) --}}
                        <div id="edit-review-box" style="display: none; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <form action="{{ route('user.reviews.update', $pesanan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3" style="margin-bottom: 12px;">
                                    <label for="edit_rating" class="form-label" style="display: block; margin-bottom: 4px; font-weight: 500;">Ubah Rating (1-5)</label>
                                    <select name="rating" id="edit_rating" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                        <option value="5" {{ $pesanan->review->rating == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (Sempurna)</option>
                                        <option value="4" {{ $pesanan->review->rating == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (Bagus)</option>
                                        <option value="3" {{ $pesanan->review->rating == 3 ? 'selected' : '' }}>⭐⭐⭐ (Cukup)</option>
                                        <option value="2" {{ $pesanan->review->rating == 2 ? 'selected' : '' }}>⭐⭐ (Kurang)</option>
                                        <option value="1" {{ $pesanan->review->rating == 1 ? 'selected' : '' }}>⭐ (Buruk)</option>
                                    </select>
                                </div>
                                <div class="mb-3" style="margin-bottom: 12px;">
                                    <label for="edit_review" class="form-label" style="display: block; margin-bottom: 4px; font-weight: 500;">Ubah Ulasan Anda</label>
                                    <textarea name="review" id="edit_review" class="form-control" rows="3" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ $pesanan->review->review }}</textarea>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.getElementById('edit-review-box').style.display='none'; document.getElementById('view-review-box').style.display='block';">Batal</button>
                                </div>
                            </form>
                        </div>

                    @else
                        <p class="text-muted mb-0" style="font-size: 0.9rem; color: #64748b;">Form ulasan akan tersedia setelah status pesanan diubah menjadi "Selesai" oleh fotografer.</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- ================= KOLOM KANAN ================= --}}
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