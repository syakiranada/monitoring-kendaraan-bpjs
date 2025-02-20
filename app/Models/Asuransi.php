<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asuransi extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'asuransi';

    // Specify the primary key (optional, as Laravel will automatically use 'id_asuransi' as primary key)
    protected $primaryKey = 'id_asuransi';

    protected $fillable = [
        'user_id',
        'id_kendaraan',
        'tahun',
        'tgl_bayar',
        'polis',
        'bukti_bayar_asuransi',
        'tgl_perlindungan_awal',
        'tgl_perlindungan_akhir',
        'nominal',
        'biaya_asuransi_lain',
        'jml_bayar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Define the relationship with the Kendaraan model
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}