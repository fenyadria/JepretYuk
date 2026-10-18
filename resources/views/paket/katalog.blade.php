@extends('layouts.app')
@section('title', 'Katalog Paket Foto - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header" style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 2rem; margin-bottom: 6px;">Pilihan Paket Foto</h1>
        <p style="color: var(--text-muted); font-size: 1rem;">Pilihan paket foto studio untuk berbagai momen Anda.</p>
    </div>

    {{-- Pencarian Paket --}}
    <div class="card" style="margin-bottom: 28px; padding: 16px;">
        <form method="GET" action="{{ route('klien.paket.index') }}" style="display: flex; align-items: center; gap: 12px; width: 100%; box-sizing: border-box; flex-wrap: wrap;">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama paket..." style="flex: 1; min-width: 200px;">
            <select name="urut" class="form-control" style="max-width: 190px;" onchange="this.form.submit()">
                <option value="termurah" {{ request('urut') === 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                <option value="termahal" {{ request('urut') === 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request('q'))
                <a href="{{ route('klien.paket.index') }}" class="btn btn-outline-dark">Reset</a>
            @endif
        </form>
    </div>

    @if($pakets->isEmpty())
        <div class="card">
            <div class="table-empty">
                Belum ada paket foto yang cocok dengan pencarian Anda.
            </div>
        </div>
    @else
        <div class="paket-grid">
            @foreach($pakets as $paket)
                <div class="paket-card" style="display: flex; flex-direction: column; height: 100%;">
                    @if($paket->foto)
                        <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" class="paket-card-img" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="paket-card-img-placeholder" style="height: 200px; display: flex; align-items: center; justify-content: center; background: #e2e8f0; color: #64748b;">Foto</div>
                    @endif

                    <div class="paket-card-body" style="display: flex; flex-direction: column; flex: 1; padding: 20px;">
                        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px; color: #1e293b;">{{ $paket->nama_paket }}</h3>
                        
                        <p class="paket-desc" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #64748b; font-size: 0.9rem; margin-bottom: 12px; line-height: 1.5;">
                            {{ $paket->deskripsi }}
                        </p>
                        
                        <div style="margin-top: auto;">
                            <div class="paket-price" style="font-size: 1.15rem; font-weight: 800; color: #d4a853; margin-bottom: 16px;">
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </div>

                            <div style="display: flex; gap: 8px; align-items: stretch; position: relative; z-index: 2;">
                                <button type="button" class="btn btn-outline-dark" style="flex: 1; height: 40px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.88rem; box-sizing: border-box; cursor: pointer;" onclick="bukaModal({{ $paket->id }})">
                                    Lihat Detail
                                </button>

                                @auth
                                    @if(auth()->user()->role === 'klien')
                                        <a href="{{ route('pesanan.create', ['paket_id' => $paket->id]) }}" class="btn btn-accent" style="flex: 1; height: 40px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.88rem; box-sizing: border-box; text-decoration: none;">
                                            Pesan Paket
                                        </a>
                                    @else
                                        <a href="{{ route('paket.edit', $paket) }}" class="btn btn-outline-dark" style="flex: 1; height: 40px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.88rem; box-sizing: border-box; text-decoration: none;">
                                            Edit Paket
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-accent" style="flex: 1; height: 40px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.88rem; box-sizing: border-box; text-decoration: none;">
                                        Masuk
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modalDetail{{ $paket->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; justify-content: center; align-items: center; padding: 16px;">
                    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                        @if($paket->foto)
                            <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" style="width: 100%; height: 240px; object-fit: cover;">
                        @endif
                        <div style="border-bottom: 1px solid #e2e8f0; padding: 16px 20px;">
                            <h5 style="font-weight: 700; font-size: 1.15rem; color: #1e293b; margin: 0;">{{ $paket->nama_paket }}</h5>
                        </div>
                        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
                            <div style="font-size: 1.3rem; font-weight: 800; color: #d4a853; margin-bottom: 12px;">
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </div>
                            <h6 style="font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 6px;">Deskripsi & Fasilitas:</h6>
                            <p style="color: #475569; line-height: 1.6; white-space: pre-line; margin: 0; font-size: 0.95rem;">
                                {{ $paket->deskripsi ?? 'Tidak ada rincian deskripsi untuk paket ini.' }}
                            </p>
                        </div>
                        <div style="border-top: 1px solid #e2e8f0; padding: 14px 20px; display: flex; justify-content: flex-end; gap: 8px; background: #f8fafc;">
                            <button type="button" onclick="tutupModal({{ $paket->id }})" class="btn btn-outline-secondary" style="font-size: 0.9rem; cursor: pointer;">Tutup</button>
                            @auth
                                @if(auth()->user()->role === 'klien')
                                    <a href="{{ route('pesanan.create', ['paket_id' => $paket->id]) }}" class="btn btn-accent" style="font-size: 0.9rem; text-decoration: none;">Pesan Paket Ini</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-accent" style="font-size: 0.9rem; text-decoration: none;">Masuk untuk Memesan</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function bukaModal(id) {
        const modal = document.getElementById('modalDetail' + id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }
    
    function tutupModal(id) {
        const modal = document.getElementById('modalDetail' + id);
        if (modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection
