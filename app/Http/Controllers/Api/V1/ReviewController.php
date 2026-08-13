<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ReviewAssignment;
use App\Services\ReviewRoundService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $assignments = ReviewAssignment::where('reviewer_id', $user->id)
            ->with(['round.submission', 'response'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($assignments);
    }

    public function respond(Request $request, ReviewAssignment $assignment)
    {
        $user = $request->user();

        if ($assignment->reviewer_id !== $user->id) {
            abort(403, 'You are not assigned to this review.');
        }

        $validated = $request->validate([
            'response' => 'required|in:accept,decline',
            'decline_reason' => 'nullable|string|max:1000',
        ]);

        if ($validated['response'] === 'accept') {
            $assignment->update(['status' => 'accepted']);
        } else {
            $assignment->update([
                'status' => 'declined',
                'decline_reason' => $validated['decline_reason'],
            ]);
        }

        return response()->json($assignment);
    }

    public function submitReview(Request $request, ReviewAssignment $assignment)
    {
        $user = $request->user();

        if ($assignment->reviewer_id !== $user->id) {
            abort(403, 'You are not assigned to this review.');
        }

        $validated = $request->validate([
            'recommendation' => 'required|in:accept,minor_revision,major_revision,reject',
            'score' => 'nullable|numeric|min:0|max:100',
            'rubric_scores' => 'nullable|array',
            'private_comment' => 'required|string|min:50|max:5000',
            'public_comment' => 'required|string|min:50|max:3000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (isset($validated['attachment'])) {
            $validated['attachment_path'] = $validated['attachment']->store('reviews', 'private_upload');
            unset($validated['attachment']);
        }

        $validated['submitted_at'] = now();

        $response = $assignment->response()->updateOrCreate(
            ['review_assignment_id' => $assignment->id],
            $validated
        );

        $assignment->update(['status' => 'completed']);

        return response()->json($response, 201);
    }
}
