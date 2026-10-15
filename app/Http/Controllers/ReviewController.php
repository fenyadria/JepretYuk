<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function store(Request $request, $pesananId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $pesanan = Pesanan::findOrFail($pesananId);

        if ($pesanan->user_id !== auth()->id() || $pesanan->status !== 'completed') {
            return back()->with('error', 'Anda tidak dapat memberikan ulasan untuk pesanan ini.');
        }

        if (Review::where('pesanan_id', $pesanan->id)->exists()) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        Review::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => auth()->id(),
            'photographer_id' => $pesanan->photographer_id, 
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    public function photographerReviews()
    {
        $photographerId = auth()->id();
        
        $reviews = Review::with(['user', 'pesanan'])
                        ->where('photographer_id', $photographerId)
                        ->latest()
                        ->paginate(10);

        $averageRating = Review::where('photographer_id', $photographerId)->avg('rating');

        return view('photographer.reviews.index', compact('reviews', 'averageRating'));
    }
}