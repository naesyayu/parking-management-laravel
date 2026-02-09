<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaParkir extends Model
{
    protected $table = 'area_parkir';
    protected $primaryKey = 'id_area';
    public $timestamps = false;

    protected $fillable = [
        'lokasi',
        'keterangan',
    ];

    // ========================================
    // BOOT METHOD - CASCADE DELETE
    // ========================================
    protected static function boot()
    {
        parent::boot();

        // Saat area parkir dihapus, hapus juga data terkait
        static::deleting(function($areaParkir) {
            // Hapus area_kapasitas yang terkait
            $areaParkir->kapasitas()->delete();
            
            // Log cascade delete
            \Log::info('Cascade delete area_kapasitas for area_parkir: ' . $areaParkir->id_area, [
                'lokasi' => $areaParkir->lokasi,
                'deleted_kapasitas_count' => $areaParkir->kapasitas()->count()
            ]);
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================
    
    public function kapasitas()
    {
        return $this->hasMany(AreaKapasitas::class, 'id_area', 'id_area');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiParkir::class, 'id_area', 'id_area');
    }
}