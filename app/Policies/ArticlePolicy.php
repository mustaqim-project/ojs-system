<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine if the user can view the article.
     */
    public function view(User $user, Article $article): bool
    {
        if ($user->hasRole(['super-admin', 'system-admin'])) {
            return true;
        }

        // Author can view their own article
        if ($article->author_id === $user->id) {
            return true;
        }

        // Editor can view articles in their journal
        $hasRole = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->where('model_has_roles.journal_id', $article->journal_id)
            ->whereIn('roles.name', ['managing-editor', 'section-editor', 'editor'])
            ->exists();

        if ($hasRole) {
            return true;
        }

        // Published articles are public
        if ($article->status === 'published') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update the article.
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->hasRole(['super-admin', 'system-admin'])) {
            return true;
        }

        // Only author can update their own draft
        return $article->author_id === $user->id
            && in_array($article->status, ['draft', 'revision_required']);
    }

    /**
     * Determine if the user can delete the article.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->hasRole(['super-admin', 'system-admin'])) {
            return true;
        }

        // Only author can delete their own draft
        return $article->author_id === $user->id
            && $article->status === 'draft';
    }

    /**
     * Determine if the user can submit the article.
     */
    public function submit(User $user, Article $article): bool
    {
        return $article->author_id === $user->id
            && $article->status === 'draft';
    }

    /**
     * Determine if the user can withdraw the article.
     */
    public function withdraw(User $user, Article $article): bool
    {
        return $article->author_id === $user->id
            && in_array($article->status, ['submitted', 'screening']);
    }
}
