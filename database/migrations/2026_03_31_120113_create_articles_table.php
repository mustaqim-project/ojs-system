<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel articles - artikel jurnal dengan full workflow
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained('issues')->nullOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_editor_id')->nullable()->constrained('users')->nullOnDelete();

            // Informasi artikel
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract');
            $table->string('keywords')->nullable(); // Comma separated
            $table->string('language', 10)->default('id');

            // File
            $table->string('manuscript_file'); // File manuskrip utama
            $table->string('revision_file')->nullable(); // File revisi
            $table->string('cover_letter')->nullable(); // Surat pengantar

            // Status workflow
            $table->enum('status', [
                'submitted',
                'under_review',
                'revision_required',
                'accepted',
                'rejected',
                'waiting_payment',
                'payment_uploaded',
                'payment_verification',
                'paid',
                'published',
            ])->default('submitted');

            // Metadata publikasi
            $table->integer('pages_start')->nullable();
            $table->integer('pages_end')->nullable();
            $table->string('doi')->nullable();

            // Catatan
            $table->text('editor_note')->nullable(); // Catatan dari editor
            $table->text('author_note')->nullable(); // Catatan dari author

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('status');
            $table->index(['journal_id', 'status']);
            $table->index('author_id');
            $table->index('assigned_editor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
