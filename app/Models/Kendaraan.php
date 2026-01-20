<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';

    protected $fillable = [
        'plat_nomor',
        'id_pemilik',
        'id_tipe',
        'status',
    ];

    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }

    public function tipe()
    {
        return $this->belongsTo(TipeKendaraan::class, 'id_tipe');
    }
}
