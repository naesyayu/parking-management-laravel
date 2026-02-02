<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;    

    protected $table = 'roles';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'role_user',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }

    /**
     * Check apakah role adalah Owner
     */
    public function isOwner(): bool
    {
        return strtolower($this->role_user) === 'owner';
    }

    /**
     * Check apakah role adalah Admin
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role_user) === 'admin';
    }

    /**
     * Check apakah role adalah Petugas
     */
    public function isPetugas(): bool
    {
        return in_array(strtolower($this->role_user), ['petugas parkir', 'petugas']);
    }

    /**
     * Check permission - HYBRID METHOD
     * Cek dari JSON dulu, kalau tidak ada generate by role
     */
    public function hasPermission(string $permission): bool
    {
        // Jika ada permissions JSON, gunakan itu
        if ($this->permissions && is_array($this->permissions)) {
            return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
        }

        // Fallback: Generate by role_user
        return $this->generatePermissionByRole($permission);
    }

    /**
     * Generate permission berdasarkan role_user
     */
    private function generatePermissionByRole(string $permission): bool
    {
        // Owner: Akses semua
        if ($this->isOwner()) {
            return true;
        }

        // Admin: Semua kecuali transaksi
        if ($this->isAdmin()) {
            return !in_array($permission, ['transaksi']);
        }

        // Petugas: Hanya transaksi
        if ($this->isPetugas()) {
            return $permission === 'transaksi';
        }

        return false;
    }

    /**
     * Get permissions array - HYBRID METHOD
     */
    public function getPermissions(): array
    {
        // Jika ada permissions JSON, gunakan itu
        if ($this->permissions && is_array($this->permissions)) {
            return $this->permissions;
        }

        // Fallback: Generate by role_user
        if ($this->isOwner()) {
            return [
                'transaksi' => true,
                'master_data' => false,
                'laporan' => true,
                'activity_log' => false,
                'user_management' => false,
            ];
        }

        if ($this->isAdmin()) {
            return [
                'transaksi' => false,
                'master_data' => true,
                'laporan' => true,
                'activity_log' => true,
                'user_management' => true,
            ];
        }

        if ($this->isPetugas()) {
            return [
                'transaksi' => true,
                'master_data' => false,
                'laporan' => false,
                'activity_log' => false,
                'user_management' => false,
            ];
        }

        return [];
    }
}
