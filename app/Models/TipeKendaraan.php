<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKendaraan extends Model
{
    protected $table = 'tipe_kendaraan';
    protected $primaryKey = 'id_tipe';
    public $timestamps = false;

    protected $fillable = [
        'tipe_kendaraan',
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'id_tipe');
    }

    public function areaKapasitas()
    {
        return $this->hasMany(AreaKapasitas::class, 'id_tipe');
    }

    public function tarifParkir()
    {
        return $this->hasMany(TarifParkir::class, 'id_tipe');
    }

}
