<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel pesanans untuk menyimpan data pesanan klien.
     */
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('paket_id')->constrained('paket_fotos')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('lokasi');
            $table->enum('status', ['menunggu', 'disetujui', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel pesanans.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
