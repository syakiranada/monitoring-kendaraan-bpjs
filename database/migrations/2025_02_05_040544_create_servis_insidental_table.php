<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servis_insidental', function (Blueprint $table) {
            $table->id('id_servis_insidental');
            $table->unsignedBigInteger('id_kendaraan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_peminjaman')->nullable(); // Tambahkan kolom untuk relasi ke peminjaman
            $table->integer('harga');
            $table->string('lokasi', 100);
            $table->string('deskripsi', 200);
            $table->string('bukti_bayar')->nullable(); // simpan path di sini
            $table->string('bukti_fisik')->nullable(); // simpan path di sini
            $table->date('tgl_servis');

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');
            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade'); // Relasi ke tabel peminjaman

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servis_insidental');
    }
};
