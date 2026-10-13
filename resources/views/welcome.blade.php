@extends('layouts.app')
@section('title', 'JepretYuk - Pesan Fotografer Profesional')

@section('content')
    {{-- Hero --}}
    <div class="hero">
        <h1>Abadikan Momen <span>Berharga</span> Anda</h1>
        <p>Pesan fotografer profesional untuk setiap momen spesial dalam hidup Anda. Mudah, cepat, dan terpercaya.</p>
        @guest
            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn btn-accent">Daftar Sekarang</a>
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
            </div>
        @else
            <div class="hero-actions">
                @if(Auth::user()->role === 'fotografer')
                    <a href="{{ route('paket.index') }}" class="btn btn-accent">Kelola Paket</a>
                    <a href="{{ route('fotografer.pesanan.index') }}" class="btn btn-outline">Lihat Pesanan</a>
                @else
                    <a href="{{ route('pesanan.create') }}" class="btn btn-accent">Pesan Sekarang</a>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline">Pesanan Saya</a>
                @endif
            </div>
        @endguest
    </div>

    {{-- Features --}}
    <div class="container">
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Pilih Paket</h3>
                <p>Berbagai paket fotografi tersedia sesuai kebutuhan dan budget Anda, dari prewedding hingga wisuda.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Atur Jadwal</h3>
                <p>Tentukan tanggal dan lokasi sesi foto Anda. Kami yang menyesuaikan dengan jadwal fotografer.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📸</div>
                <h3>Sesi Foto</h3>
                <p>Fotografer profesional kami siap mengabadikan momen terbaik Anda dengan hasil berkualitas.</p>
            </div>
        </div>
    </div>
@endsection
