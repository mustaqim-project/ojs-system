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

    public function isAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'system-admin', 'admin']);
    }

    public function isEditor(): bool
    {
        return $this->hasRole(['managing-editor', 'section-editor', 'editor']);
    }

    public function isReviewer(): bool
    {
        return $this->hasRole('reviewer');
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author');
    }

    public function isFinance(): bool
    {
        return $this->hasRole('finance');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) return true;
            }
            return false;
        }
        // Check via Spatie first, fallback to legacy role column
        if (method_exists($this, 'hasRoleViaSpatie')) {
            return parent::hasRole($roles) || $this->role === $roles;
        }
        return $this->role === $roles;
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
            ->wherePivot('team_id', $journalId)
            ->where('name', $role)
            ->exists();
    }

    public function assignedJournals()
    {
        $journalIds = $this->roles()
            ->pluck('team_id')
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
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
