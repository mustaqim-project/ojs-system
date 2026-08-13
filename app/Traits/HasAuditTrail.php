<?php

namespace App\Traits;

use App\Services\AuditService;

trait HasAuditTrail
{
    protected static function bootHasAuditTrail(): void
    {
        static::created(function ($model) {
            AuditService::log(
                class_basename($model),
                'created',
                $model,
                null,
                $model->toArray()
            );
        });

        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getChanges() as $key => $value) {
                if (!in_array($key, ['updated_at', 'created_at'])) {
                    $changes[$key] = $value;
                }
            }
            if (!empty($changes)) {
                AuditService::log(
                    class_basename($model),
                    'updated',
                    $model,
                    $model->getOriginal(),
                    $changes
                );
            }
        });

        static::deleted(function ($model) {
            AuditService::log(
                class_basename($model),
                'deleted',
                $model,
                $model->toArray(),
                null
            );
        });
    }
}
