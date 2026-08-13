<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApcFee extends Model
{
    protected $fillable = [
        'journal_id',
        'amount',
        'currency',
        'section',
        'is_active',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSection($query, ?string $section)
    {
        if ($section) {
            return $query->where('section', $section);
        }
        return $query->whereNull('section');
    }
}
