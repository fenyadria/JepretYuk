@extends('layouts.app')
@section('title', 'Edit Profil Saya - JepretYuk')

@section('content')
<div class="container">
    
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="margin-bottom: 4px;">Edit Profil Saya</h1>
        </div>
    </div>

    <div class="card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-top: 16px;">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label for="name" style="display: block; font-weight: 600; font-size: 0.9rem; color: #334155; margin-bottom: 6px;">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                @error('name')
                    <span style="color: #dc2626; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: #334155; margin-bottom: 6px;">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                @error('email')
                    <span style="color: #dc2626; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

            <div style="margin-bottom: 16px;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #1e293b; margin: 0 0 2px 0;">Ubah Kata Sandi</h3>
                <span style="font-size: 0.82rem; color: #64748b;">Kosongkan bagian ini jika tidak ingin mengubah kata sandi Anda.</span>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="password" style="display: block; font-weight: 500; font-size: 0.9rem; color: #334155; margin-bottom: 6px;">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                @error('password')
                    <span style="color: #dc2626; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="password_confirmation" style="display: block; font-weight: 500; font-size: 0.9rem; color: #334155; margin-bottom: 6px;">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div style="display: flex; gap: 12px; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('pesanan.index') }}" class="btn btn-outline-dark">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection