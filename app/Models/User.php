<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'affiliation',
        'institution_id',
        'phone',
        'bio',
        'orcid',
        'orcid_token',
        'google_id',
        'google_token',
        'is_active',
        'avatar_path',
        'research_interest',
        'scopus_id',
        'google_scholar_url',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'is_active'          => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ===========================
    // ROLE HELPERS (Backward Compat)
    // ===========================
    // Helper di bawah tetap memeriksa kolom legacy `users.role` sebagai
    // fallback, SELAIN role Spatie. Method `hasRole()` TIDAK dioverride
    // (biarkan dari Spatie) agar pipeline permission internal tetap bekerja.

    public function isAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'system-admin', 'admin'])
            || $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->hasRole(['managing-editor', 'section-editor', 'editor'])
            || $this->role === 'editor';
    }

    public function isReviewer(): bool
    {
        return $this->hasRole('reviewer')
            || $this->role === 'reviewer';
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author')
            || $this->role === 'author';
    }

    public function isFinance(): bool
    {
        return $this->hasRole('finance')
            || $this->role === 'finance';
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) return 'admin.dashboard';
        if ($this->isEditor()) return 'editor.dashboard';
        if ($this->isReviewer()) return 'reviewer.dashboard';
        if ($this->isAuthor()) return 'author.dashboard';
        return 'public.home';
    }

    // ===========================
    // JOURNAL-SCOPED HELPERS
    // ===========================

    public function hasJournalRole(string $role, int $journalId): bool
    {
        return $this->roles()
            ->wherePivot('journal_id', $journalId)
            ->where('name', $role)
            ->exists();
    }

    public function assignedJournals()
    {
        $journalIds = $this->roles()
            ->pluck('journal_id')
            ->filter()
            ->unique();

        return Journal::whereIn('id', $journalIds)->get();
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

    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'author_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'editor_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeByRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role)
              ->orWhereHas('roles', fn ($sq) => $sq->where('name', $role));
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
