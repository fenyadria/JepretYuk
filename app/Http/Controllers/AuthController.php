<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form registrasi.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi user baru.
     * User bisa mendaftar sebagai 'fotografer' atau 'klien'.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:fotografer,klien',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Auth::login($user);

        return $this->redirectByRole($user);
    }

    /**
     * Menampilkan halaman form login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses login user.
     * Setelah login, user diarahkan ke dashboard sesuai role-nya.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }

    /**
     * Mengarahkan user ke halaman dashboard sesuai role.
     * - Fotografer -> dashboard fotografer (kelola paket & pesanan)
     * - Klien     -> dashboard klien (lihat paket & buat pesanan)
     */
    private function redirectByRole($user)
    {
        if ($user->role === 'fotografer') {
            return redirect('/fotografer/paket')->with('success', 'Selamat datang, Fotografer!');
        }

        return redirect('/klien/pesanan')->with('success', 'Selamat datang, Klien!');
    }
}
