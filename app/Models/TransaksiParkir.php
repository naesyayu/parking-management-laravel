<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiParkir extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_parkir';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'kode_tiket',
        'id_kendaraan',
        'id_area',
        'waktu_masuk',
        'waktu_keluar',
        'durasi_jam',
        'id_tarif',
        'diskon',
        'total_bayar',
        'id_user',
        'id_member',
        'id_metode',
        'status',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'diskon' => 'decimal:2',        
        'total_bayar' => 'decimal:2',
    ];

    /**
    * =====================
    * RELASI MASTER DATA
    * =====================
    */


    public function kendaraan()
    {
    return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }


    public function areaParkir()
    {
    return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }


    public function tarifParkir()
    {
    return $this->belongsTo(TarifParkir::class, 'id_tarif', 'id_tarif');
    }


    public function user()
    {
    return $this->belongsTo(User::class, 'id_user', 'id_user');
    }


    public function member()
    {
    return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }


    public function metodePembayaran()
    {
    return $this->belongsTo(MetodePembayaran::class, 'id_metode', 'id_metode');
    }

    /**
     * =====================
     * RELASI KE ACTIVITY LOG
     * =====================
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * =============================================
     * SCOPES
     * =============================================
     */

    /**
     * Scope untuk transaksi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('waktu_keluar', today());
    }

    /**
     * Scope untuk transaksi berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk transaksi dalam rentang tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('waktu_keluar', [$startDate, $endDate]);
    }

    /**
     * =============================================
     * ACCESSOR & MUTATOR
     * =============================================
     */

    /**
     * Format total bayar ke rupiah
     */
    public function getTotalBayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Format diskon ke rupiah
     */
    public function getDiskonFormattedAttribute()
    {
        return 'Rp ' . number_format($this->diskon, 0, ',', '.');
    }

    /**
     * Get tarif asli (sebelum diskon)
     */
    public function getTarifAsliAttribute()
    {
        return $this->total_bayar + $this->diskon;
    }
}