<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel reviews - proses review peer oleh reviewer
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // Status assignment
            $table->enum('status', [
                'pending',      // Baru di-assign, belum dikonfirmasi reviewer
                'accepted',     // Reviewer menerima tugas review
                'declined',     // Reviewer menolak tugas review
                'in_progress',  // Sedang dalam proses review
                'completed',    // Review selesai
            ])->default('pending');

            // Konten review
            $table->text('recommendation')->nullable(); // accept/minor/major/reject
            $table->text('comments_to_author')->nullable();
            $table->text('comments_to_editor')->nullable(); // Confidential
            $table->string('review_file')->nullable(); // File review jika ada

            // Skor review (1-5)
            $table->tinyInteger('originality_score')->nullable();
            $table->tinyInteger('methodology_score')->nullable();
            $table->tinyInteger('relevance_score')->nullable();
            $table->tinyInteger('writing_score')->nullable();

            $table->timestamp('due_date')->nullable(); // Batas waktu review
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['article_id', 'status']);
            $table->index('reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
