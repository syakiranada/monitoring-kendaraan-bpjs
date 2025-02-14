<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function cekFisik()
    {
        return $this->hasMany(CekFisik::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function servisInsidental()
    {
        return $this->hasMany(ServisInsidental::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function servisRutin()
    {
        return $this->hasMany(ServisRutin::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function bbm()
    {
        return $this->hasMany(BBM::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function asuransi()
    {
        return $this->hasMany(Asuransi::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function pajak()
    {
        return $this->hasMany(Pajak::class, 'id_kendaraan', 'id_kendaraan');
    }
    
}
