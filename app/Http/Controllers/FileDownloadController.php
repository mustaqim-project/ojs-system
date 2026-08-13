<?php

namespace App\Http\Controllers;

use App\Models\ArticleGalley;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FileDownloadController extends Controller
{
    /**
     * Download a private file after authorization check.
     */
    public function download(string $model, int $id, string $field)
    {
        $record = $this->resolveModel($model, $id);

        // Policy check
        $this->authorize('view', $record);

        $path = $record->$field;
        if (!$path || !Storage::disk('private_upload')->exists($path)) {
            abort(404, 'File not found.');
        }

        $filename = $record->original_filename ?? basename($path);

        return Storage::disk('private_upload')->download($path, $filename);
    }

    /**
     * Generate a temporary signed URL for file access (15 min expiry).
     */
    public function signedUrl(string $model, int $id, string $field): string
    {
        return URL::temporarySignedRoute(
            'files.download',
            now()->addMinutes(15),
            ['model' => $model, 'id' => $id, 'field' => $field]
        );
    }

    /**
     * Resolve model class from string identifier.
     */
    private function resolveModel(string $model, int $id)
    {
        return match ($model) {
            'submission-file' => SubmissionFile::findOrFail($id),
            'payment-proof'   => Payment::findOrFail($id),
            'receipt'         => Receipt::findOrFail($id),
            'article-galley'  => ArticleGalley::findOrFail($id),
            default           => abort(400, "Unknown model: {$model}"),
        };
    }
}
