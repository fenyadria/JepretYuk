@extends('layouts.app')
@section('title', 'Tambah Admin - JepretYuk')

@section('content')
<div class="container" style="max-width: 600px;">
    <div class="page-header">
        <h1>Tambah Admin</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('fotografer.admin.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email') }}" placeholder="admin@email.com" required>
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

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">Simpan</button>
                <a href="{{ route('fotografer.admin.index') }}" class="btn btn-outline-dark">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
