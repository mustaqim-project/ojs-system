<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
        'module',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForModel($query, string $modelType, int $modelId)
    {
        return $query->where('model_type', $modelType)->where('model_id', $modelId);
    }
}
