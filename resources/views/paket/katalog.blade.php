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
        <form method="GET" action="{{ auth()->check() && auth()->user()->role === 'klien' ? route('klien.paket.index') : route('paket.katalog') }}" style="display: flex; gap: 12px; max-width: 600px; margin: 0 auto;">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama paket...">
             <select name="urut" class="form-control" style="max-width: 190px;" onchange="this.form.submit()">
       <option value="termurah" {{ request('urut') === 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
       <option value="termahal" {{ request('urut') === 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
   </select>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request('q'))
                <a href="{{ auth()->check() && auth()->user()->role === 'klien' ? route('klien.paket.index') : route('paket.katalog') }}" class="btn btn-outline-dark">Reset</a>
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
                <div class="paket-card">
                    @if($paket->foto)
                        <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" class="paket-card-img">
                    @else
                        <div class="paket-card-img-placeholder">Foto</div>
                    @endif

                    <div class="paket-card-body">
                        <h3>{{ $paket->nama_paket }}</h3>
                        <p class="paket-desc">{{ $paket->deskripsi }}</p>
                        <div class="paket-price">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>

                        <div style="margin-top: 16px;">
                            @auth
                                @if(auth()->user()->role === 'klien')
                                    <a href="{{ route('pesanan.create', ['paket_id' => $paket->id]) }}" class="btn btn-accent" style="width: 100%; justify-content: center;">
                                        Pesan Paket
                                    </a>
                                @else
                                    <a href="{{ route('paket.edit', $paket) }}" class="btn btn-outline-dark" style="width: 100%; justify-content: center;">
                                        Edit Paket
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-accent" style="width: 100%; justify-content: center;">
                                    Masuk untuk Memesan
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
