<?php

namespace App\Http\Controllers;

use App\Models\PaketFoto;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan filter & pencarian lengkap:
     * - Status (menunggu, disetujui, ditolak, selesai)
     * - Tanggal sesi foto
     * - Keyword pencarian (nama klien, lokasi, nama paket)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Pesanan::with(['klien', 'paketFoto']);

        if ($user->role === 'klien') {
            // Klien hanya melihat pesanan miliknya sendiri
            $query->where('klien_id', $user->id);
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tanggal Sesi
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Fitur Pencarian (Lokasi, Nama Klien, Nama Paket)
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword, $user) {
                $q->where('lokasi', 'like', "%{$keyword}%")
                  ->orWhereHas('paketFoto', function ($sub) use ($keyword) {
                      $sub->where('nama_paket', 'like', "%{$keyword}%");
                  });

                // Jika fotografer, bisa juga mencari berdasarkan nama klien
                if ($user->role === 'fotografer') {
                    $q->orWhereHas('klien', function ($sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
                }
            });
        }

        $pesanans = $query->latest()->get();

        return view('pesanan.index', compact('pesanans'));
    }

    /**
     * Menampilkan form untuk membuat pesanan baru (hanya klien).
     */
    public function create()
    {
        $pakets = PaketFoto::all();

        return view('pesanan.create', compact('pakets'));
    }

    /**
     * Menyimpan pesanan baru ke database dengan validasi bentrok jadwal & upload bukti transfer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_id'         => 'required|exists:paket_fotos,id',
            'tanggal'          => 'required|date|after_or_equal:today',
            'lokasi'           => 'required|string|max:255',
            'catatan_klien'    => 'nullable|string|max:1000',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'tanggal.after_or_equal' => 'Tanggal sesi foto minimal hari ini atau hari berikutnya.',
            'bukti_pembayaran.image' => 'File bukti pembayaran harus berupa gambar (JPG, PNG).',
            'bukti_pembayaran.max'   => 'Ukuran file bukti pembayaran maksimal 3MB.',
        ]);

        // CEK VALIDASI BENTROK JADWAL:
        // Cek apakah studio sudah memiliki pesanan 'disetujui' pada tanggal tersebut
        $jadwalBentrok = Pesanan::where('tanggal', $request->tanggal)
            ->where('status', 'disetujui')
            ->exists();

        if ($jadwalBentrok) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal' => 'Jadwal pada tanggal ' . date('d-m-Y', strtotime($request->tanggal)) . ' sudah dibooking dan disetujui untuk sesi lain. Silakan pilih tanggal lain.'
                ]);
        }

        $data = [
            'klien_id'      => Auth::id(),
            'paket_id'      => $request->paket_id,
            'tanggal'       => $request->tanggal,
            'lokasi'        => $request->lokasi,
            'catatan_klien' => $request->catatan_klien,
            'status'        => 'menunggu',
        ];

        // Upload bukti pembayaran jika diunggah
        if ($request->hasFile('bukti_pembayaran')) {
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        Pesanan::create($data);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat dan menunggu persetujuan fotografer.');
    }

    /**
     * Menampilkan detail lengkap satu pesanan.
     * - Fotografer: bisa melihat detail pesanan manapun.
     * - Klien: hanya bisa melihat detail pesanan miliknya sendiri.
     */
    public function show(Pesanan $pesanan)
    {
        $user = Auth::user();

        // Klien hanya boleh melihat pesanan miliknya sendiri
        if ($user->role === 'klien' && $pesanan->klien_id !== $user->id) {
            return redirect()->route('pesanan.index')->with('error', 'Akses ditolak: Anda tidak memiliki akses ke pesanan ini.');
        }

        $pesanan->load(['klien', 'paketFoto']);

        return view('pesanan.show', compact('pesanan'));
    }

    /**
     * Menampilkan form edit pesanan.
     */
    public function edit(Pesanan $pesanan)
    {
        $user = Auth::user();

        // Fotografer tidak bisa mengedit pesanan yang sudah berstatus 'selesai' (final/terkunci)
        if ($user->role === 'fotografer') {
            if ($pesanan->status === 'selesai') {
                return redirect()->route('fotografer.pesanan.index')
                    ->with('error', 'Pesanan #' . $pesanan->id . ' sudah berstatus SELESAI dan terkunci permanen. Status tidak dapat diubah kembali.');
            }
        }

        // Klien hanya bisa edit pesanan miliknya yang masih berstatus 'menunggu'
        if ($user->role === 'klien') {
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses (' . $pesanan->status . '), tidak dapat diubah.');
            }
        }

        $pakets = PaketFoto::all();

        return view('pesanan.edit', compact('pesanan', 'pakets'));
    }

    /**
     * Memperbarui data pesanan di database:
     * - Fotografer: Ubah status + beri catatan/alasan penolakan (hanya jika belum selesai)
     * - Klien: Ubah detail pesanan + update bukti transfer / catatan (hanya jika masih menunggu)
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        $user = Auth::user();

        if ($user->role === 'fotografer') {
            // Validasi: Status yang sudah selesai tidak dapat diubah lagi
            if ($pesanan->status === 'selesai') {
                return redirect()->route('fotografer.pesanan.index')
                    ->with('error', 'Akses ditolak: Pesanan #' . $pesanan->id . ' sudah berstatus SELESAI dan bersifat final.');
            }

            $request->validate([
                'status'             => 'required|in:menunggu,disetujui,ditolak,selesai',
                'catatan_fotografer' => 'nullable|string|max:1000',
            ]);

            // Jika status hendak disetujui, pastikan tidak bentrok dengan pesanan disetujui lainnya pada tanggal yang sama
            if ($request->status === 'disetujui') {
                $bentrok = Pesanan::where('tanggal', $pesanan->tanggal)
                    ->where('status', 'disetujui')
                    ->where('id', '!=', $pesanan->id)
                    ->exists();

                if ($bentrok) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'status' => 'Tidak dapat menyetujui pesanan ini karena sudah ada pesanan lain yang DISETUJUI pada tanggal ' . date('d-m-Y', strtotime($pesanan->tanggal)) . '.'
                        ]);
                }
            }

            $pesanan->update([
                'status'             => $request->status,
                'catatan_fotografer' => $request->catatan_fotografer,
            ]);

            return redirect()->route('fotografer.pesanan.index')->with('success', 'Status pesanan #' . $pesanan->id . ' berhasil diperbarui menjadi ' . $request->status . '.');
        } else {
            // Role Klien
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses, tidak bisa diedit.');
            }

            $request->validate([
                'paket_id'         => 'required|exists:paket_fotos,id',
                'tanggal'          => 'required|date|after_or_equal:today',
                'lokasi'           => 'required|string|max:255',
                'catatan_klien'    => 'nullable|string|max:1000',
                'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            ]);

            // Cek bentrok jika tanggal diubah
            if ($pesanan->tanggal !== $request->tanggal) {
                $bentrok = Pesanan::where('tanggal', $request->tanggal)
                    ->where('status', 'disetujui')
                    ->exists();

                if ($bentrok) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'tanggal' => 'Jadwal pada tanggal ' . date('d-m-Y', strtotime($request->tanggal)) . ' sudah penuh/disetujui. Silakan pilih tanggal lain.'
                        ]);
                }
            }

            $data = $request->only(['paket_id', 'tanggal', 'lokasi', 'catatan_klien']);

            // Update bukti pembayaran jika diupload ulang
            if ($request->hasFile('bukti_pembayaran')) {
                if ($pesanan->bukti_pembayaran) {
                    Storage::disk('public')->delete($pesanan->bukti_pembayaran);
                }
                $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            }

            $pesanan->update($data);

            return redirect()->route('pesanan.index')->with('success', 'Pesanan #' . $pesanan->id . ' berhasil diperbarui.');
        }
    }

    /**
     * Upload bukti pembayaran secara terpisah (quick action untuk klien).
     */
    public function uploadBukti(Request $request, Pesanan $pesanan)
    {
        $user = Auth::user();

        if ($pesanan->klien_id !== $user->id) {
            return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'bukti_pembayaran.required' => 'File bukti pembayaran wajib dipilih.',
            'bukti_pembayaran.image'    => 'File harus berupa gambar.',
            'bukti_pembayaran.max'      => 'Ukuran file maksimal 3MB.',
        ]);

        if ($pesanan->bukti_pembayaran) {
            Storage::disk('public')->delete($pesanan->bukti_pembayaran);
        }

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        $pesanan->update(['bukti_pembayaran' => $path]);

        return redirect()->route('pesanan.index')->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    /**
     * Menghapus pesanan dari database beserta berkas bukti pembayaran.
     */
    public function destroy(Pesanan $pesanan)
    {
        $user = Auth::user();

        if ($user->role === 'klien') {
            if ($pesanan->klien_id !== $user->id) {
                return redirect()->route('pesanan.index')->with('error', 'Akses ditolak.');
            }
            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('pesanan.index')->with('error', 'Pesanan sudah diproses (' . $pesanan->status . '), tidak dapat dibatalkan/dihapus.');
            }
        }

        // Hapus file bukti pembayaran dari storage jika ada
        if ($pesanan->bukti_pembayaran) {
            Storage::disk('public')->delete($pesanan->bukti_pembayaran);
        }

        $pesanan->delete();

        $redirectRoute = ($user->role === 'fotografer') ? 'fotografer.pesanan.index' : 'pesanan.index';
        return redirect()->route($redirectRoute)->with('success', 'Pesanan #' . $pesanan->id . ' berhasil dihapus.');
    }

    /**
     * Menyimpan ulasan dan rating dari Klien untuk pesanan yang sudah selesai.
     */
    public function storeReview(Request $request, $pesananId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $pesanan = Pesanan::findOrFail($pesananId);

        // Pastikan user yang login adalah klien pemilik pesanan dan statusnya sudah 'selesai'
        if ($pesanan->klien_id !== Auth::id() || $pesanan->status !== 'selesai') {
            return back()->with('error', 'Anda tidak dapat memberikan ulasan untuk pesanan ini.');
        }

        // Cek apakah ulasan untuk pesanan ini sudah pernah dibuat sebelumnya
        if (\App\Models\Review::where('pesanan_id', $pesanan->id)->exists()) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        \App\Models\Review::create([
            'pesanan_id'      => $pesanan->id,
            'user_id'         => Auth::id(),
            'photographer_id' => $pesanan->photographer_id ?? 1, // Sesuaikan jika ada kolom id fotografer, atau default ke ID admin/fotografer utama
            'rating'          => $request->rating,
            'review'          => $request->review,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    /**
     * Menampilkan daftar ulasan dan rata-rata rating di sisi Fotografer.
     */
    public function photographerReviews()
    {
        $reviews = \App\Models\Review::with(['user', 'pesanan'])
                        ->latest()
                        ->paginate(10);

        $averageRating = \App\Models\Review::avg('rating');

        return view('fotografer\admin.reviews.index', compact('reviews', 'averageRating'));
    }

    /**
     * Memperbarui ulasan dan rating yang sudah pernah dikirim oleh Klien.
     */
    public function updateReview(Request $request, $pesananId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $pesanan = Pesanan::findOrFail($pesananId);

        // Pastikan pesanan milik klien yang login dan statusnya 'selesai'
        if ($pesanan->klien_id !== Auth::id() || $pesanan->status !== 'selesai') {
            return back()->with('error', 'Akses ditolak.');
        }

        $review = \App\Models\Review::where('pesanan_id', $pesanan->id)->first();

        if (!$review) {
            return back()->with('error', 'Ulasan tidak ditemukan.');
        }

        // Update data ulasan
        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Ulasan Anda berhasil diperbarui!');
    }
}
