<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemilik extends Model
{
    use SoftDeletes;

    protected $table = 'pemilik';
    protected $primaryKey = 'id_pemilik';

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'id_pemilik');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'id_pemilik');
    }

    public function transaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class, 'id_pemilik', 'id_pemilik');
    }
    
}
