<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="JepretYuk - Sistem pesan fotografer profesional untuk setiap momen spesial Anda.">
    <title>@yield('title', 'JepretYuk - Pesan Fotografer')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="container nav-inner">
            <a href="/" class="nav-brand">JepretYuk</a>
            <div class="nav-links">
                @guest
                    <a href="{{ route('paket.katalog') }}">Pilihan Paket</a>
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @else
                    <span class="nav-user">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->role === 'fotografer')
                        <a href="{{ route('paket.index') }}">Kelola Paket</a>
                        <a href="{{ route('fotografer.pesanan.index') }}">Daftar Pesanan</a>
                        <a href="{{ route('fotografer.admin.index') }}">Kelola Admin</a>
                        <a href="{{ route('fotografer.reviews') }}" style="color: #d4a853; font-weight: 600;">Ulasan Klien</a>
                    @else
                        <a href="{{ route('klien.paket.index') }}">Pilihan Paket</a>
                        <a href="{{ route('pesanan.index') }}">Pesanan Saya</a>
                        <a href="{{ route('pesanan.create') }}">Buat Pesanan</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="nav-logout">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="main-content">
        @if(session('success') || session('error'))
            <div class="container" style="padding-top: 20px;">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
