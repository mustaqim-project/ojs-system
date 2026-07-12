<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent navigation item.
     */
    public function parent()
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }

    /**
     * Get the child navigation items.
     */
    public function children()
    {
        return $this->hasMany(Navigation::class, 'parent_id')->orderBy('order');
    }
}
