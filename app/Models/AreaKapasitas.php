<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaKapasitas extends Model
{
    protected $table = 'area_kapasitas';
    protected $primaryKey = 'id_kapasitas';

    public $timestamps = false;

    protected $fillable = [
        'id_area',
        'id_tipe',
        'kapasitas',
    ];

    public function area()
    {
        return $this->belongsTo(AreaParkir::class, 'id_area');
    }

    public function tipe()
    {
        return $this->belongsTo(TipeKendaraan::class, 'id_tipe');
    }
}
