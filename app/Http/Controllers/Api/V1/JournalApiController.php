<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalApiController extends Controller
{
    /**
     * Tampilkan list jurnal aktif.
     */
    public function index(): AnonymousResourceCollection
    {
        $journals = Journal::where('is_active', true)->orderBy('title')->get();
        return JournalResource::collection($journals);
    }

    /**
     * Detail satu jurnal.
     */
    public function show(int $id): JournalResource
    {
        $journal = Journal::where('is_active', true)->findOrFail($id);
        return new JournalResource($journal);
    }
}
