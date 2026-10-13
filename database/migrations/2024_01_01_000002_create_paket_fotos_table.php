<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel paket_fotos untuk menyimpan data paket fotografi.
     */
    public function up(): void
    {
        Schema::create('paket_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->text('deskripsi');
            $table->decimal('harga', 12, 2);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel paket_fotos.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_fotos');
    }
};
