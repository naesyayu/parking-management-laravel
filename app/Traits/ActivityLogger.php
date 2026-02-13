<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait ActivityLogger
{
    /**
     * =============================================
     * LOG CREATE ACTION
     * =============================================
     */
    protected function logCreate($model, string $entityName, array $additionalMetadata = [])
    {
        $modelData = $model->toArray();
        
        ActivityLog::log(
            action: "tambah_{$entityName}",
            description: "Menambahkan {$entityName}: " . $this->getModelIdentifier($model),
            idTransaksi: $this->getTransaksiId($model),
            metadata: array_merge([
                'entity_type' => get_class($model),
                'entity_id' => $model->getKey(),
            ], $additionalMetadata),
            before: null, // No before data for create
            after: $modelData // After data shows created record
        );
    }

    /**
     * =============================================
     * LOG UPDATE ACTION - WITH BEFORE/AFTER
     * =============================================
     */
    protected function logUpdate($model, string $entityName, array $originalData, array $additionalMetadata = [])
    {
        $newData = $model->fresh()->toArray();
        
        ActivityLog::log(
            action: "edit_{$entityName}",
            description: "Mengubah {$entityName}: " . $this->getModelIdentifier($model),
            idTransaksi: $this->getTransaksiId($model),
            metadata: array_merge([
                'entity_type' => get_class($model),
                'entity_id' => $model->getKey(),
            ], $additionalMetadata),
            before: $originalData,
            after: $newData
        );
    }

    /**
     * =============================================
     * LOG DELETE ACTION
     * =============================================
     */
    protected function logDelete($model, string $entityName, array $additionalMetadata = [])
    {
        $modelData = $model->toArray();
        
        ActivityLog::log(
            action: "hapus_{$entityName}",
            description: "Menghapus {$entityName}: " . $this->getModelIdentifier($model),
            idTransaksi: $this->getTransaksiId($model),
            metadata: array_merge([
                'entity_type' => get_class($model),
                'entity_id' => $model->getKey(),
                'soft_delete' => method_exists($model, 'trashed'),
            ], $additionalMetadata),
            before: $modelData, // Before shows deleted data
            after: null // After is null (deleted)
        );
    }

    /**
     * =============================================
     * LOG RESTORE ACTION
     * =============================================
     */
    protected function logRestore($model, string $entityName, array $additionalMetadata = [])
    {
        $modelData = $model->toArray();
        
        ActivityLog::log(
            action: "restore_{$entityName}",
            description: "Memulihkan {$entityName}: " . $this->getModelIdentifier($model),
            idTransaksi: $this->getTransaksiId($model),
            metadata: array_merge([
                'entity_type' => get_class($model),
                'entity_id' => $model->getKey(),
            ], $additionalMetadata),
            before: null, // Before was deleted
            after: $modelData // After shows restored data
        );
    }

    /**
     * =============================================
     * GET MODEL IDENTIFIER (for description)
     * =============================================
     */
    private function getModelIdentifier($model): string
    {
        // Try common identifier fields
        $identifiers = [
            'name', 'nama', 'username', 'title', 'judul',
            'plat_nomor', 'kode_tiket', 'kode_area', 'role_user',
            'tipe_kendaraan', 'metode_bayar', 'nama_level'
        ];
        
        foreach ($identifiers as $field) {
            if (isset($model->$field)) {
                return $model->$field;
            }
        }
        
        // Fallback to primary key
        return '#' . $model->getKey();
    }

    /**
     * =============================================
     * GET TRANSACTION ID IF EXISTS
     * =============================================
     */
    private function getTransaksiId($model): ?int
    {
        // Check if model has id_transaksi
        if (isset($model->id_transaksi)) {
            return $model->id_transaksi;
        }
        
        // Check if model IS a transaction
        if (get_class($model) === 'App\Models\TransaksiParkir') {
            return $model->getKey();
        }
        
        return null;
    }
}