<?php

namespace App\Http\Controllers;

use App\Models\PaketFoto;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard fotografer beserta statistik lengkap:
     * - Total pesanan & breakdown status
     * - Estimasi omset/pendapatan dari pesanan yang disetujui / selesai
     * - Pesanan terbaru yang memerlukan konfirmasi
     * - Jadwal sesi foto terdekat
     * - Paket foto terlaris (paling banyak dipesan)
     */
    public function fotografer()
    {
        $totalPesanan = Pesanan::count();
        $pesananMenunggu = Pesanan::where('status', 'menunggu')->count();
        $pesananDisetujui = Pesanan::where('status', 'disetujui')->count();
        $pesananSelesai = Pesanan::where('status', 'selesai')->count();
        $pesananDitolak = Pesanan::where('status', 'ditolak')->count();

        // Total omset / estimasi pendapatan dari pesanan yang disetujui & selesai
        $totalPendapatan = Pesanan::whereIn('status', ['disetujui', 'selesai'])
            ->join('paket_fotos', 'pesanans.paket_id', '=', 'paket_fotos.id')
            ->sum('paket_fotos.harga');

        $totalKlien = User::where('role', 'klien')->count();
        $totalPaket = PaketFoto::count();

        // 5 Pesanan terbaru yang butuh konfirmasi
        $pesananTerbaru = Pesanan::with(['klien', 'paketFoto'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        // Jadwal pemotretan terdekat yang sudah disetujui
        $jadwalTerdekat = Pesanan::with(['klien', 'paketFoto'])
            ->where('status', 'disetujui')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->take(5)
            ->get();

        // 3 paket yang paling banyak dipesan
        $paketTerlaris = PaketFoto::withCount('pesanans')
            ->orderByDesc('pesanans_count')
            ->take(3)
            ->get();

        return view('dashboard.fotografer', compact(
            'totalPesanan',
            'pesananMenunggu',
            'pesananDisetujui',
            'pesananSelesai',
            'pesananDitolak',
            'totalPendapatan',
            'totalKlien',
            'totalPaket',
            'pesananTerbaru',
            'jadwalTerdekat',
            'paketTerlaris'
        ));
    }

    /**
     * Menampilkan dashboard ringkasan untuk klien:
     * - Total pesanan saya
     * - Pesanan aktif / jadwal terdekat
     */
    public function klien()
    {
        $userId = Auth::id();

        $totalPesananSaya = Pesanan::where('klien_id', $userId)->count();
        $pesananMenunggu = Pesanan::where('klien_id', $userId)->where('status', 'menunggu')->count();
        $pesananDisetujui = Pesanan::where('klien_id', $userId)->where('status', 'disetujui')->count();
        $pesananSelesai = Pesanan::where('klien_id', $userId)->where('status', 'selesai')->count();

        $pesananAktif = Pesanan::with('paketFoto')
            ->where('klien_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $paketPopuler = PaketFoto::latest()->take(3)->get();

        return view('dashboard.klien', compact(
            'totalPesananSaya',
            'pesananMenunggu',
            'pesananDisetujui',
            'pesananSelesai',
            'pesananAktif',
            'paketPopuler'
        ));
    }
}