<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->enum('status', ['invited', 'accepted', 'declined', 'completed', 'overdue'])->default('invited');
            $table->dateTime('due_date')->nullable();
            $table->text('decline_reason')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_assignments');
    }
};
