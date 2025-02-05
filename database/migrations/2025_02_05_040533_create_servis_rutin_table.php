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
        Schema::create('servis_rutin', function (Blueprint $table) {
            $table->id('id_servis_rutin');
            $table->unsignedBigInteger('id_kendaraan');
            $table->unsignedBigInteger('user_id');
            // $table->string('tipe', 10);
            $table->integer('harga');
            $table->integer('kilometer');
            $table->string('lokasi', 100);
            // $table->binary('bukti_bayar')->nullable();
            // $table->binary('bukti_fisik')->nullable();
            $table->string('bukti_bayar')->nullable(); // Simpan path di sini
            $table->string('bukti_fisik')->nullable(); // Simpan path di sini
            // $table->string('status', 20);
            $table->date('tgl_servis_real');
            $table->date('tgl_servis_selanjutnya');
            
            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servis_rutin');
    }
};
