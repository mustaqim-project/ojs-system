<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Mass assignment protection
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'affiliation',
        'phone',
        'bio',
        'orcid',
        'orcid_token',
        'google_id',
        'google_token',
        'is_active',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ===========================
    // ROLE HELPER METHODS
    // ===========================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isReviewer(): bool
    {
        return $this->role === 'reviewer';
    }

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isReader(): bool
    {
        return $this->role === 'reader';
    }

    /**
     * Cek apakah user bisa mengakses dashboard tertentu
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    /**
     * URL redirect sesuai role setelah login
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'    => 'admin.dashboard',
            'editor'   => 'editor.dashboard',
            'reviewer' => 'reviewer.dashboard',
            'author'   => 'author.dashboard',
            default    => 'public.home',
        };
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function assignedArticles()
    {
        return $this->hasMany(Article::class, 'assigned_editor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'author_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'editor_id');
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
