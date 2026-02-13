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
     * ENHANCED LOG METHOD - WITH BEFORE/AFTER
     * =============================================
     * 
     * @param string $action - Action type
     * @param string|null $description - Description
     * @param int|null $idTransaksi - Transaction ID
     * @param array $metadata - Additional metadata
     * @param array|null $before - Data before change
     * @param array|null $after - Data after change
     */
    public static function log(
        string $action,
        string $description = null,
        int $idTransaksi = null,
        array $metadata = [],
        array $before = null,
        array $after = null
    ): self {
        // Build complete metadata
        $completeMetadata = [
            'timestamp' => now()->toIso8601String(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'referer' => Request::header('referer'),
        ];
        
        // Add before/after if provided
        if ($before !== null) {
            $completeMetadata['before'] = $before;
        }
        
        if ($after !== null) {
            $completeMetadata['after'] = $after;
        }
        
        // Add changes summary if both before and after exist
        if ($before !== null && $after !== null) {
            $completeMetadata['changes'] = self::detectChanges($before, $after);
        }
        
        // Merge with custom metadata
        $completeMetadata = array_merge($completeMetadata, $metadata);
        
        return self::create([
            'id_user' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'id_transaksi' => $idTransaksi,
            'metadata' => $completeMetadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
    
    /**
     * =============================================
     * LOG FAILED LOGIN (No User ID)
     * =============================================
     */
    public static function logFailedLogin(string $username, string $reason = 'Invalid credentials'): self
    {
        return self::create([
            'id_user' => null, // No user ID because login failed
            'action' => 'login_failed',
            'description' => "Failed login attempt for username: {$username}",
            'id_transaksi' => null,
            'metadata' => [
                'username_attempted' => $username,
                'reason' => $reason,
                'timestamp' => now()->toIso8601String(),
                'url' => Request::fullUrl(),
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
    
    /**
     * =============================================
     * LOG PAGE NAVIGATION
     * =============================================
     */
    public static function logNavigation(string $routeName, string $url): self
    {
        return self::create([
            'id_user' => Auth::id(),
            'action' => 'page_view',
            'description' => "User navigated to: {$routeName}",
            'id_transaksi' => null,
            'metadata' => [
                'route_name' => $routeName,
                'url' => $url,
                'previous_url' => Request::header('referer'),
                'timestamp' => now()->toIso8601String(),
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
    
    /**
     * =============================================
     * DETECT CHANGES BETWEEN BEFORE & AFTER
     * =============================================
     */
    private static function detectChanges(array $before, array $after): array
    {
        $changes = [];
        
        // Find all keys
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));
        
        foreach ($allKeys as $key) {
            $oldValue = $before[$key] ?? null;
            $newValue = $after[$key] ?? null;
            
            // Skip if unchanged
            if ($oldValue === $newValue) {
                continue;
            }
            
            // Skip sensitive fields
            if (in_array($key, ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            
            $changes[$key] = [
                'from' => $oldValue,
                'to' => $newValue,
            ];
        }
        
        return $changes;
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
     * SCOPE: FAILED LOGINS ONLY
     * =============================================
     */
    public function scopeFailedLogins($query)
    {
        return $query->where('action', 'login_failed');
    }
    
    /**
     * =============================================
     * SCOPE: PAGE VIEWS ONLY
     * =============================================
     */
    public function scopePageViews($query)
    {
        return $query->where('action', 'page_view');
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
        return $this->user?->username ?? 'Guest/System';
    }
    
    /**
     * =============================================
     * ACCESSOR: Has Changes
     * =============================================
     */
    public function getHasChangesAttribute()
    {
        return isset($this->metadata['changes']) && count($this->metadata['changes']) > 0;
    }
    
    /**
     * =============================================
     * ACCESSOR: Changes Count
     * =============================================
     */
    public function getChangesCountAttribute()
    {
        return isset($this->metadata['changes']) ? count($this->metadata['changes']) : 0;
    }
}