<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $journals = Journal::query()
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->paginate(20);

        return response()->json($journals);
    }

    public function show(Journal $journal)
    {
        return response()->json($journal->load('settings', 'bankAccounts'));
    }
}
