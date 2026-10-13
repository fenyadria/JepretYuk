<?php

namespace App\Http\Controllers;

use App\Models\PaketFoto;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan.
     * - Fotografer: melihat SEMUA pesanan dari semua klien.
     * - Klien: hanya melihat pesanan miliknya sendiri.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'fotografer') {
            // Fotografer bisa lihat semua pesanan beserta data klien dan paket
            $pesanans = Pesanan::with(['klien', 'paketFoto'])->latest()->get();
        } else {
            // Klien hanya lihat pesanan miliknya sendiri
            $pesanans = Pesanan::with('paketFoto')
                ->where('klien_id', $user->id)
                ->latest()
                ->get();
        }

        return view('pesanan.index', compact('pesanans'));
    }

    /**
     * Menampilkan form untuk membuat pesanan baru (hanya klien).
     * Mengirim data paket foto yang tersedia ke view.
     */
    public function create()
    {
        $pakets = PaketFoto::all();

        return view('pesanan.create', compact('pakets'));
    }

    /**
     * Menyimpan pesanan baru ke database.
     * klien_id diambil dari user yang sedang login.
     * Status default: 'menunggu'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_id' => 'required|exists:paket_fotos,id',
            'tanggal'  => 'required|date|after_or_equal:today',
            'lokasi'   => 'required|string|max:255',
        ]);

        Pesanan::create([
            'klien_id' => Auth::id(),
            'paket_id' => $request->paket_id,
            'tanggal'  => $request->tanggal,
            'lokasi'   => $request->lokasi,
            'status'   => 'menunggu',
        ]);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Menampilkan form edit pesanan.
     * - Fotografer: bisa edit status pesanan manapun.
     * - Klien: hanya bisa edit pesanan miliknya yang masih berstatus 'menunggu'.
     */
    public function edit(Pesanan $pesanan)
    {
        $user = Auth::user();

        // Klien hanya bisa edit pesanan miliknya yang masih menunggu
        if ($user->role === 'klien') {
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses, tidak bisa diedit.');
            }
        }

        $pakets = PaketFoto::all();

        return view('pesanan.edit', compact('pesanan', 'pakets'));
    }

    /**
     * Memperbarui data pesanan di database.
     * - Fotografer: bisa mengubah status pesanan (menunggu -> disetujui -> selesai).
     * - Klien: bisa mengubah detail pesanan (paket, tanggal, lokasi) selama masih 'menunggu'.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        $user = Auth::user();

        if ($user->role === 'fotografer') {
            // Fotografer hanya mengubah status pesanan
            $request->validate([
                'status' => 'required|in:menunggu,disetujui,selesai',
            ]);

            $pesanan->update([
                'status' => $request->status,
            ]);

            return redirect()->route('pesanan.index')->with('success', 'Status pesanan berhasil diperbarui.');
        } else {
            // Klien mengubah detail pesanan (hanya jika masih menunggu)
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses, tidak bisa diedit.');
            }

            $request->validate([
                'paket_id' => 'required|exists:paket_fotos,id',
                'tanggal'  => 'required|date|after_or_equal:today',
                'lokasi'   => 'required|string|max:255',
            ]);

            $pesanan->update($request->only(['paket_id', 'tanggal', 'lokasi']));

            return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
        }
    }

    /**
     * Menghapus pesanan dari database.
     * - Fotografer: bisa hapus pesanan manapun.
     * - Klien: hanya bisa hapus pesanan miliknya yang masih 'menunggu'.
     */
    public function destroy(Pesanan $pesanan)
    {
        $user = Auth::user();

        if ($user->role === 'klien') {
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses, tidak bisa dihapus.');
            }
        }

        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
