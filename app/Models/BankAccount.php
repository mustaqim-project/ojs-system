<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'journal_id',
        'bank_name',
        'account_number',
        'account_holder',
        'currency',
        'is_active',
    ];

    protected $casts = [
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
}
