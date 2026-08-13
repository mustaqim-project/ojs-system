<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Article;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function journalStats(Request $request, Journal $journal)
    {
        $stats = [
            'total_articles' => Article::where('journal_id', $journal->id)->count(),
            'published_articles' => Article::where('journal_id', $journal->id)->where('status', 'published')->count(),
            'pending_reviews' => Article::where('journal_id', $journal->id)->where('current_stage', 'under_review')->count(),
            'total_authors' => User::whereHas('articles', fn($q) => $q->where('journal_id', $journal->id))->count(),
            'total_revenue' => Payment::whereHas('invoice.submission', fn($q) => $q->where('journal_id', $journal->id))
                ->where('status', 'verified')
                ->sum('amount'),
        ];

        return response()->json($stats);
    }

    public function submissionTrend(Request $request, Journal $journal)
    {
        $trend = Article::where('journal_id', $journal->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return response()->json($trend);
    }

    public function reviewStats(Request $request, Journal $journal)
    {
        $stats = [
            'total_reviews' => \App\Models\ReviewResponse::whereHas('assignment.round.submission', fn($q) => $q->where('journal_id', $journal->id))->count(),
            'avg_score' => \App\Models\ReviewResponse::whereHas('assignment.round.submission', fn($q) => $q->where('journal_id', $journal->id))
                ->whereNotNull('score')
                ->avg('score'),
            'accept_rate' => $this->calculateAcceptRate($journal->id),
        ];

        return response()->json($stats);
    }

    private function calculateAcceptRate(int $journalId): float
    {
        $total = \App\Models\ReviewResponse::whereHas('assignment.round.submission', fn($q) => $q->where('journal_id', $journalId))->count();
        $accepted = \App\Models\ReviewResponse::whereHas('assignment.round.submission', fn($q) => $q->where('journal_id', $journalId))
            ->where('recommendation', 'accept')
            ->count();

        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }
}
