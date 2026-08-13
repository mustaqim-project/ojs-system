<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmissionFile extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'submission_version_id',
        'file_category',
        'original_filename',
        'stored_path',
        'disk',
        'mime_type',
        'size_bytes',
        'checksum_sha256',
        'virus_scan_status',
        'uploaded_by',
    ];

    public function submissionVersion()
    {
        return $this->belongsTo(SubmissionVersion::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->disk === 'public_upload') {
            return asset('upload/public/' . $this->stored_path);
        }
        return route('files.signed', [
            'model' => 'submission-file',
            'id' => $this->id,
            'field' => 'stored_path',
        ]);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }
}
