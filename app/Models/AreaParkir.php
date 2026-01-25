<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaParkir extends Model
{
    use SoftDeletes;

    protected $table = 'area_parkir';
    protected $primaryKey = 'id_area';

    protected $fillable = [
        'kode_area',
        'lokasi',
        'foto_lokasi',
    ];

    public function kapasitas()
    {
        return $this->hasMany(AreaKapasitas::class, 'id_area');
    }

    public function transaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class, 'id_area', 'id_area');
    }
}
