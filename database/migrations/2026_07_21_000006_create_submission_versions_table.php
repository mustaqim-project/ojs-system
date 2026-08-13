<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('submission_id')->constrained('articles')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->foreignId('review_round_id')->nullable()->constrained()->nullOnDelete();
            $table->string('response_to_reviewers_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_versions');
    }
};
