@extends('layouts.app')
@section('title', 'Daftar - JepretYuk')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Daftar Akun</h1>
        <p class="auth-subtitle">Buat akun baru untuk mulai menggunakan JepretYuk</p>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email') }}" placeholder="nama@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                       placeholder="Ulangi password" required>
            </div>

            <div class="form-group">
                <label>Daftar sebagai</label>
                <div class="role-options">
                    <div class="role-option">
                        <input type="radio" name="role" id="role_klien" value="klien"
                               {{ old('role', 'klien') === 'klien' ? 'checked' : '' }}>
                        <label for="role_klien">
                            <span class="role-icon">👤</span>
                            <span class="role-label">Klien</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role_fotografer" value="fotografer"
                               {{ old('role') === 'fotografer' ? 'checked' : '' }}>
                        <label for="role_fotografer">
                            <span class="role-icon">📷</span>
                            <span class="role-label">Fotografer</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-accent" style="width:100%">Daftar</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
