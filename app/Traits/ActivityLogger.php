<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait ActivityLogger
{
    /**
     * Log CRUD activity automatically
     * 
     * Usage di controller:
     * - $this->logCreate($model, 'Nama Model')
     * - $this->logUpdate($model, 'Nama Model')
     * - $this->logDelete($model, 'Nama Model')
     */

    /**
     * Log Create (Tambah)
     */
    protected function logCreate(Model $model, string $modelName, array $additionalData = [])
    {
        $primaryKey = $model->getKeyName();
        $id = $model->{$primaryKey};
        
        // Dapatkan nama yang relevan (plat_nomor, nama, dll)
        $identifier = $this->getIdentifier($model);
        
        ActivityLog::log(
            'tambah_' . strtolower(str_replace(' ', '_', $modelName)),
            "Menambah {$modelName}: {$identifier}",
            null,
            array_merge([
                'model' => get_class($model),
                'id' => $id,
                'identifier' => $identifier,
                'data' => $model->toArray(),
            ], $additionalData)
        );
    }

    /**
     * Log Update (Edit)
     */
    protected function logUpdate(Model $model, string $modelName, array $originalData = [], array $additionalData = [])
    {
        $primaryKey = $model->getKeyName();
        $id = $model->{$primaryKey};
        
        $identifier = $this->getIdentifier($model);
        
        // Deteksi perubahan
        $changes = [];
        if (!empty($originalData)) {
            foreach ($model->getAttributes() as $key => $value) {
                if (isset($originalData[$key]) && $originalData[$key] != $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value,
                    ];
                }
            }
        }
        
        ActivityLog::log(
            'edit_' . strtolower(str_replace(' ', '_', $modelName)),
            "Mengedit {$modelName}: {$identifier}",
            null,
            array_merge([
                'model' => get_class($model),
                'id' => $id,
                'identifier' => $identifier,
                'changes' => $changes,
            ], $additionalData)
        );
    }

    /**
     * Log Delete (Hapus)
     */
    protected function logDelete(Model $model, string $modelName, array $additionalData = [])
    {
        $primaryKey = $model->getKeyName();
        $id = $model->{$primaryKey};
        
        $identifier = $this->getIdentifier($model);
        
        ActivityLog::log(
            'hapus_' . strtolower(str_replace(' ', '_', $modelName)),
            "Menghapus {$modelName}: {$identifier}",
            null,
            array_merge([
                'model' => get_class($model),
                'id' => $id,
                'identifier' => $identifier,
                'deleted_data' => $model->toArray(),
            ], $additionalData)
        );
    }

    /**
     * Log Restore (Pulihkan dari Soft Delete)
     */
    protected function logRestore(Model $model, string $modelName, array $additionalData = [])
    {
        $primaryKey = $model->getKeyName();
        $id = $model->{$primaryKey};
        
        $identifier = $this->getIdentifier($model);
        
        ActivityLog::log(
            'restore_' . strtolower(str_replace(' ', '_', $modelName)),
            "Memulihkan {$modelName}: {$identifier}",
            null,
            array_merge([
                'model' => get_class($model),
                'id' => $id,
                'identifier' => $identifier,
            ], $additionalData)
        );
    }

    /**
     * Get identifier dari model (plat_nomor, nama, username, dll)
     */
    private function getIdentifier(Model $model): string
    {
        // Cek field yang umum digunakan sebagai identifier
        $identifierFields = [
            'plat_nomor',
            'nama',
            'username',
            'lokasi',
            'tipe_kendaraan',
            'metode_bayar',
            'role_user',
            'kode_tiket',
        ];
        
        foreach ($identifierFields as $field) {
            if (isset($model->{$field})) {
                return $model->{$field};
            }
        }
        
        // Fallback ke primary key
        $primaryKey = $model->getKeyName();
        return "ID: " . $model->{$primaryKey};
    }
}