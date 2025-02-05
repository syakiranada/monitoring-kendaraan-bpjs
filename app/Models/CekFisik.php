<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CekFisik extends Model
{
    use HasFactory;

    protected $table = 'cek_fisik';  
    protected $primaryKey = 'id_cek_fisik';  
    protected $fillable = [
        'id_kendaraan', 'user_id', 'tgl_cek_fisik', 'mesin', 'accu', 'air_radiator', 'air_wiper',
        'body', 'ban', 'pengharum', 'kondisi_keseluruhan', 'catatan'
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
