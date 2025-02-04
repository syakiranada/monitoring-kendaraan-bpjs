<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan'; // Nama tabel di database

    protected $primaryKey = 'id_kendaraan'; // Primary Key

    public $timestamps = true; // Mengaktifkan timestamps (created_at, updated_at)
    protected $fillable = [
        'merk',
        'tipe',
        'jenis',
        'warna',
        'plat_nomor',
        'nilai_perolehan',
        'nilai_buku',
        'tgl_pembelian',
        'aset',
        'status_ketersediaan',
        'bahan_bakar',
        'frekuensi_servis',
        'no_mesin',
        'no_rangka',
        'kapasitas',
    ];
}
