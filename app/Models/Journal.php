<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'abbreviation',
        'description',
        'issn_print',
        'issn_online',
        'cover_image',
        'publisher',
        'subject_area',
        'frequency',
        'is_active',
        'editor_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function apcFees()
    {
        return $this->hasMany(ApcFee::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'journal_id', 'model_id')
            ->withPivot('journal_id')
            ->wherePivot('journal_id', $this->id);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function publishedArticles()
    {
        return $this->articles()->where('status', 'published');
    }

    public function volumes()
    {
        return $this->hasMany(Volume::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
