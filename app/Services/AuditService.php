<?php

namespace App\Services;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditService
{
    public static function log(
        string $module,
        string $action,
        mixed $model = null,
        array $oldValues = null,
        array $newValues = null,
        ?int $userId = null,
        ?Request $request = null
    ): AuditTrail {
        $user = $userId ?? auth()->id();
        $request = $request ?? request();

        $modelType = null;
        $modelId = null;

        if ($model) {
            $modelType = is_object($model) ? get_class($model) : $model;
            $modelId = is_object($model) ? $model->getKey() : null;
        }

        return AuditTrail::create([
            'module'      => $module,
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'user_id'     => $user,
            'ip_address'  => $request?->ip(),
            'user_agent'  => $request?->userAgent(),
            'created_at'  => now(),
        ]);
    }
}
