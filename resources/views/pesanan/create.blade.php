@extends('layouts.app')
@section('title', 'Buat Pesanan - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Buat Pesanan Baru</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($pakets->isEmpty())
            <div class="alert alert-warning">
                Belum ada paket foto yang tersedia. Silakan hubungi fotografer.
            </div>
        @else
            <form method="POST" action="{{ route('pesanan.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="paket_id">Pilih Paket Foto</label>
                    <select name="paket_id" id="paket_id" class="form-control" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}" 
                                    data-deskripsi="{{ e($paket->deskripsi) }}" 
                                    data-harga="Rp {{ number_format($paket->harga, 0, ',', '.') }}"
                                    {{ old('paket_id', request('paket_id')) == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }} — Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="box-detail-paket" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 6px 0; font-size: 0.95rem; color: #1e293b; font-weight: 600;">📌 Rincian Paket Pilihan:</h5>
                    <div id="detail-harga" style="font-size: 1.15rem; font-weight: 800; color: #d4a853; margin-bottom: 8px;"></div>
                    <p id="detail-deskripsi" style="margin: 0; font-size: 0.88rem; color: #475569; line-height: 1.5; white-space: pre-line;"></p>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Sesi Foto</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                           value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control"
                           value="{{ old('lokasi') }}" placeholder="Lokasi sesi foto" required>
                </div>

                <div class="form-group">
                    <label for="catatan_klien">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan_klien" id="catatan_klien" class="form-control" rows="3"
                              placeholder="Catatan atau permintaan khusus">{{ old('catatan_klien') }}</textarea>
                </div>

                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 8px 0; font-size: 0.95rem; color: #1e293b;">
                        💳 Informasi Rekening Pembayaran
                    </h4>
                    <p style="font-size: 0.85rem; color: #475569; margin: 0 0 8px 0;">
                        Silakan lakukan transfer ke salah satu rekening berikut jika ingin langsung melampirkan bukti pembayaran/DP:
                    </p>
                    <ul style="margin: 0; padding-left: 20px; font-size: 0.88rem; color: #334155; line-height: 1.6;">
                        <li><strong>BCA:</strong> 1234-5678-90 (a.n. Studio JepretYuk)</li>
                        <li><strong>Mandiri:</strong> 0987-6543-21 (a.n. Studio JepretYuk)</li>
                        <li><strong>E-Wallet (Dana/OVO):</strong> 0812-3456-7890 (a.n. JepretYuk)</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="bukti_pembayaran">Bukti Pembayaran / DP (Opsional)</label>
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" accept="image/*">
                    <small style="color: #64748b; font-size: 13px;">Format gambar (JPG, PNG) maksimal 3MB.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent">Kirim Pesanan</button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-dark">Batal</a>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectPaket = document.getElementById('paket_id');
        const boxDetail = document.getElementById('box-detail-paket');
        const detailHarga = document.getElementById('detail-harga');
        const detailDeskripsi = document.getElementById('detail-deskripsi');

        function updatePaketDetail() {
            if (!selectPaket) return;
            
            const selectedOption = selectPaket.options[selectPaket.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const deskripsi = selectedOption.getAttribute('data-deskripsi');
                const harga = selectedOption.getAttribute('data-harga');

                detailHarga.textContent = harga;
                detailDeskripsi.textContent = deskripsi ? deskripsi : 'Tidak ada rincian deskripsi.';
                boxDetail.style.display = 'block';
            } else {
                boxDetail.style.display = 'none';
            }
        }

        selectPaket.addEventListener('change', updatePaketDetail);
        
        // Jalankan saat pertama kali dimuat (jika paket sudah terpilih dari query URL / old input)
        updatePaketDetail();
    });
</script>
@endsection
