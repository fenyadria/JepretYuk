<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar semua fotografer / admin studio.
     */
    public function index()
    {
        $admins = User::where('role', 'fotografer')->latest()->get();

        return view('fotografer.admin.index', compact('admins'));
    }

    /**
     * Menampilkan form untuk menambahkan fotografer / admin baru.
     */
    public function create()
    {
        return view('fotografer.admin.create');
    }

    /**
     * Menyimpan data fotografer / admin baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama fotografer wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email tersebut sudah terdaftar dalam sistem.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'fotografer',
        ]);

        return redirect()->route('fotografer.admin.index')
            ->with('success', 'Akun fotografer/admin baru atas nama "' . $request->name . '" berhasil dibuat.');
    }

    /**
     * Menghapus akun fotografer/admin (dengan proteksi agar tidak bisa menghapus diri sendiri).
     */
    public function destroy(User $user)
    {
        // Proteksi: Tidak boleh menghapus akun sendiri yang sedang login
        if ($user->id === Auth::id()) {
            return redirect()->route('fotografer.admin.index')
                ->with('error', 'Akses ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // Proteksi: Pastikan user yang dihapus memang ber-role fotografer
        if ($user->role !== 'fotografer') {
            return redirect()->route('fotografer.admin.index')
                ->with('error', 'Akses ditolak: User bukan fotografer.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('fotografer.admin.index')
            ->with('success', 'Akun fotografer "' . $nama . '" berhasil dihapus dari sistem.');
    }
}
