<?php

namespace App\Http\Controllers;

use App\Models\PaketFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaketFotoController extends Controller
{
    /**
     * Menampilkan daftar paket foto dengan fitur pencarian nama atau deskripsi.
     */
    public function index(Request $request)
    {
        $query = PaketFoto::query();

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where('nama_paket', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%");
        }

        $pakets = $query->latest()->get();

        return view('paket.index', compact('pakets'));
    }

    /**
     * Menampilkan katalog paket foto khusus untuk klien dan publik (tampilan etalase foto).
     */
    public function katalog(Request $request)
    {
        $query = PaketFoto::query();

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where('nama_paket', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%");
        }

        // Urutkan paket sesuai pilihan: termurah, termahal, atau terbaru (default)
        if ($request->urut === 'termurah') {
            $query->orderBy('harga', 'asc');
        } elseif ($request->urut === 'termahal') {
            $query->orderBy('harga', 'desc');
        } else {
            $query->latest();
        }

        $pakets = $query->get();

        return view('paket.katalog', compact('pakets'));
    }

    /**
     * Menampilkan form untuk membuat paket foto baru.
     */
    public function create()
    {
        return view('paket.create');
    }

    /**
     * Menyimpan paket foto baru ke database.
     * Jika ada file foto yang diupload, simpan ke storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'harga'      => 'required|numeric|min:0',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nama_paket', 'deskripsi', 'harga']);

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('paket_fotos', 'public');
        }

        PaketFoto::create($data);

        return redirect()->route('paket.index')->with('success', 'Paket foto berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit untuk paket foto tertentu.
     */
    public function edit(PaketFoto $paket)
    {
        return view('paket.edit', compact('paket'));
    }

    /**
     * Memperbarui data paket foto di database.
     * - Jika kotak "Hapus foto" dicentang (dan tidak ada foto baru), foto dihapus.
     * - Jika ada foto baru, hapus foto lama dan simpan yang baru.
     */
    public function update(Request $request, PaketFoto $paket)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'harga'      => 'required|numeric|min:0',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nama_paket', 'deskripsi', 'harga']);

        // Hapus foto jika dicentang dan tidak ada foto baru yang diupload
        if ($request->boolean('hapus_foto') && !$request->hasFile('foto')) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }
            $data['foto'] = null;
        }

        // Upload foto baru jika ada, hapus foto lama
        if ($request->hasFile('foto')) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }
            $data['foto'] = $request->file('foto')->store('paket_fotos', 'public');
        }

        $paket->update($data);

        return redirect()->route('paket.index')->with('success', 'Paket foto berhasil diperbarui.');
    }

    /**
     * Menghapus paket foto dari database.
     * Jika ada file foto, hapus juga dari storage.
     */
    public function destroy(PaketFoto $paket)
    {
        // Proteksi: Cegah penghapusan jika paket masih digunakan oleh pesanan klien
        if ($paket->pesanans()->exists()) {
            return redirect()->route('paket.index')
                ->with('error', 'Gagal menghapus: Paket foto "' . $paket->nama_paket . '" masih digunakan oleh riwayat/pesanan klien.');
        }

        // Hapus file foto dari storage jika ada
        if ($paket->foto) {
            Storage::disk('public')->delete($paket->foto);
        }

        $paket->delete();

        return redirect()->route('paket.index')->with('success', 'Paket foto berhasil dihapus.');
    }
}