<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJournalScope
{
    /**
     * Handle an incoming request - verify user has access to the requested journal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $journalId = $request->route('journal')
            ?? $request->route('journal_id')
            ?? $request->input('journal_id')
            ?? session('current_journal_id');

        if ($journalId && auth()->check()) {
            $user = auth()->user();

            // Super admin / system admin have access to all journals
            if ($user->hasRole(['super-admin', 'system-admin'])) {
                return $next($request);
            }

            // Check if user has any role scoped to this journal
            $hasAccess = $user->roles()
                ->wherePivot('journal_id', $journalId)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'You do not have access to this journal.');
            }
        }

        return $next($request);
    }
}
