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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan');
            $table->string('merk', 50); // Merk kendaraan (varchar 50)
            $table->string('tipe', 40); // Tipe kendaraan (varchar 40)
            $table->string('jenis', 10); // Jenis kendaraan (varchar 10)
            $table->string('warna', 20); // Warna kendaraan (varchar 20)
            $table->string('plat_nomor', 20)->unique(); // Plat nomor kendaraan (varchar 20)
            $table->integer('nilai_perolehan'); // Nilai perolehan kendaraan (int)
            $table->integer('nilai_buku'); // Nilai buku kendaraan (int)
            $table->date('tgl_pembelian'); // Tanggal pembelian kendaraan (date)
            $table->string('aset', 10); // Aset (varchar 10)
            $table->string('status_ketersediaan', 20); // Status ketersediaan (varchar 20)
            $table->string('bahan_bakar', 75); // Jenis bahan bakar (varchar 25)
            $table->integer('frekuensi_servis'); // Frekuensi servis (int)
            $table->char('no_mesin', 17); // Nomor mesin (char 17)
            $table->string('no_rangka', 20); // Nomor rangka (varchar 20)
            $table->integer('kapasitas'); // Kapasitas kendaraan (int)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
