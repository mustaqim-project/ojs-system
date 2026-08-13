<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_assignment_id')->constrained()->cascadeOnDelete();
            $table->enum('recommendation', ['accept', 'minor_revision', 'major_revision', 'reject'])->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('rubric_scores')->nullable();
            $table->text('private_comment')->nullable();
            $table->text('public_comment')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_responses');
    }
};
