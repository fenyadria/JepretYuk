@extends('layouts.app')
@section('title', 'Masuk - JepretYuk')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Masuk</h1>
        <p class="auth-subtitle">Masuk ke akun JepretYuk Anda</p>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-accent" style="width:100%">Masuk</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
