<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipeKendaraan extends Model
{
    use SoftDeletes;

    protected $table = 'tipe_kendaraan';
    protected $primaryKey = 'id_tipe';
    public $timestamps = true;

    protected $fillable = [
        'kode_tipe',
        'tipe_kendaraan',
        'deskripsi_tipe',
    ];

    // ========================================
    // BOOT METHOD - CASCADE DELETE
    // ========================================
    protected static function boot()
    {
        parent::boot();

        // Saat tipe kendaraan dihapus, hapus juga data terkait
        static::deleting(function($tipeKendaraan) {
            // Hapus area_kapasitas yang terkait
            $tipeKendaraan->areaKapasitas()->delete();
            
            // Log cascade delete
            \Log::info('Cascade delete area_kapasitas for tipe_kendaraan: ' . $tipeKendaraan->id_tipe, [
                'tipe' => $tipeKendaraan->tipe_kendaraan,
                'deleted_kapasitas_count' => $tipeKendaraan->areaKapasitas()->count()
            ]);
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================
    
    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'id_tipe', 'id_tipe');
    }

    public function areaKapasitas()
    {
        return $this->hasMany(AreaKapasitas::class, 'id_tipe', 'id_tipe');
    }

    public function tarifParkir()
    {
        return $this->hasMany(TarifParkir::class, 'id_tipe', 'id_tipe');
    }
}