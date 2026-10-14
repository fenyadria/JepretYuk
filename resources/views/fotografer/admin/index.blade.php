@extends('layouts.app')
@section('title', 'Kelola Admin - JepretYuk')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Kelola Admin</h1>
        <a href="{{ route('fotografer.admin.create') }}" class="btn btn-accent">+ Tambah Admin</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Terdaftar</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $index => $admin)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $admin->name }}</strong>
                                @if($admin->id === Auth::id())
                                    <span class="badge" style="background: #e2e8f0; color: #475569; margin-left: 6px; font-size: 0.75rem;">Anda</span>
                                @endif
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}</td>
                            <td style="text-align: center;">
                                @if($admin->id === Auth::id())
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">Aktif</span>
                                @else
                                    <form action="{{ route('fotografer.admin.destroy', $admin) }}" method="POST"
                                          onsubmit="return confirm('Hapus akun admin \'{{ $admin->name }}\'?')"
                                          style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty">Belum ada akun admin lainnya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
