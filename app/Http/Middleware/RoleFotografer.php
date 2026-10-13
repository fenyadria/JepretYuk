<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleFotografer
{
    /**
     * Middleware: Hanya mengizinkan user dengan role 'fotografer'.
     * Jika bukan fotografer, redirect ke halaman utama dengan pesan error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'fotografer') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses ditolak. Anda bukan fotografer.');
    }
}
