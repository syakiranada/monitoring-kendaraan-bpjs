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
        Schema::create('bbm', function (Blueprint $table) {
            $table->id('id_bbm'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key untuk pengguna
            $table->unsignedBigInteger('id_kendaraan'); // Foreign Key untuk kendaraan
            $table->unsignedBigInteger('id_peminjaman')->nullable(); // Tambahkan kolom untuk relasi ke peminjaman
            $table->unsignedBigInteger('nominal')->nullable(); // Jumlah nominal pengisian BBM
            $table->string('jenis_bbm', 25)->nullable();// Jenis BBM (varchar 25)
            $table->date('tgl_isi')->nullable(); // Tanggal pengisian BBM
            $table->timestamps(); // Created_at & Updated_at

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');
            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade'); // Relasi ke tabel peminjaman
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbm');
    }
};