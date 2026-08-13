<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('review_round_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('decided_by')->constrained('users');
            $table->enum('decision', ['accept', 'reject', 'minor_revision', 'major_revision', 'desk_reject']);
            $table->text('comment_to_author')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_decisions');
    }
};
