@extends('layouts.app')
@section('title', 'Kelola Paket Foto - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Paket Foto</h1>
        <a href="{{ route('paket.create') }}" class="btn btn-accent">+ Tambah Paket</a>
    </div>

    @if($pakets->isEmpty())
        <div class="card">
            <div class="table-empty">
                Belum ada paket foto. Klik tombol "Tambah Paket" untuk membuat paket baru.
            </div>
        </div>
    @else
        <div class="paket-grid">
            @foreach($pakets as $paket)
                <div class="paket-card">
                    @if($paket->foto)
                        <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" class="paket-card-img">
                    @else
                        <div class="paket-card-img-placeholder">📸</div>
                    @endif

                    <div class="paket-card-body">
                        <h3>{{ $paket->nama_paket }}</h3>
                        <p class="paket-desc">{{ $paket->deskripsi }}</p>
                        <div class="paket-price">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>

                        <div class="paket-card-actions">
                            <a href="{{ route('paket.edit', $paket) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('paket.destroy', $paket) }}" method="POST"
                                  onsubmit="return confirm('⚠️ PERINGATAN HAPUS PAKET:\nApakah Anda yakin ingin menghapus paket foto \'{{ $paket->nama_paket }}\'?\n\nPaket ini tidak dapat dihapus jika masih terkait dengan pesanan klien.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
