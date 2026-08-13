<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('submission_version_id')->constrained()->cascadeOnDelete();
            $table->enum('file_category', ['manuscript', 'cover_letter', 'supplementary', 'revision', 'response_to_reviewers'])->nullable();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('disk')->default('private_upload');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum_sha256')->nullable();
            $table->enum('virus_scan_status', ['pending', 'clean', 'infected', 'error'])->default('pending');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
