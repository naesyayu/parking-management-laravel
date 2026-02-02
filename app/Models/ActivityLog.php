<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_user',
        'action',
        'description',
        'id_transaksi',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke TransaksiParkir
     */
    public function transaksi()
    {
        return $this->belongsTo(TransaksiParkir::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * =============================================
     * HELPER METHOD: LOG AKTIVITAS
     * =============================================
     * 
     * Contoh penggunaan:
     * ActivityLog::log('login', 'User berhasil login');
     * ActivityLog::log('transaksi_masuk', 'Kendaraan masuk', $idTransaksi, ['plat' => 'B 1234 ABC']);
     */
    public static function log(
        string $action,
        string $description = null,
        int $idTransaksi = null,
        array $metadata = []
    ): self {
        return self::create([
            'id_user' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'id_transaksi' => $idTransaksi,
            'metadata' => array_merge($metadata, [
                'timestamp' => now()->toIso8601String(),
            ]),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * =============================================
     * SCOPE: FILTER BY ACTION
     * =============================================
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * =============================================
     * SCOPE: FILTER BY USER
     * =============================================
     */
    public function scopeByUser($query, $idUser)
    {
        return $query->where('id_user', $idUser);
    }

    /**
     * =============================================
     * SCOPE: FILTER BY DATE RANGE
     * =============================================
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * =============================================
     * SCOPE: TODAY
     * =============================================
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * =============================================
     * ACCESSOR: Format tanggal untuk Indonesia
     * =============================================
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * =============================================
     * ACCESSOR: Nama user
     * =============================================
     */
    public function getUsernameAttribute()
    {
        return $this->user?->username ?? 'System';
    }
}