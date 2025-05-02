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
        Schema::create('pajak', function (Blueprint $table) {
            $table->id('id_pajak'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key ke tabel users
            $table->unsignedBigInteger('id_kendaraan'); // Foreign Key
            $table->integer('tahun'); // Tahun pajak (buat generate id)
            $table->date('tgl_bayar')->nullable(); // Tanggal pembayaran realtime
            $table->date('tgl_jatuh_tempo'); // Tanggal jatuh tempo
            $table->string('bukti_bayar_pajak')->nullable(); // Bukti pembayaran
            // $table->string('status', 25); // Status (varchar 10)
            $table->bigInteger('nominal')->nullable(); // Nominal pembayaran
            $table->bigInteger('biaya_pajak_lain')->nullable(); // Biaya lain-lain
            $table->timestamps(); // Created_at & Updated_at

            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak');
    }
};