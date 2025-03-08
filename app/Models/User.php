<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'peran',
        'status',
    ];

    public function peminjaman() {
        return $this->hasMany(Peminjaman::class, 'user_id', 'id');
    }

    public function cekFisik()
    {
        return $this->hasMany(CekFisik::class, 'user_id', 'id'); 
    }

    public function servisInsidental()
    {
        return $this->hasMany(ServisInsidental::class, 'user_id', 'id');
    }

    public function servisRutin()
    {
        return $this->hasMany(ServisRutin::class, 'user_id', 'id');
    }

    public function bbm() {
        return $this->hasMany(BBM::class, 'user_id', 'id');
    }

    public function asuransi() {
        return $this->hasMany(Asuransi::class, 'user_id', 'id');
    }

    public function pajak() {
        return $this->hasMany(Pajak::class, 'user_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
