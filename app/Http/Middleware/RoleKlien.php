<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleKlien
{
    /**
     * Middleware: Hanya mengizinkan user dengan role 'klien'.
     * Jika bukan klien, redirect ke halaman utama dengan pesan error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'klien') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses ditolak. Anda bukan klien.');
    }
}
