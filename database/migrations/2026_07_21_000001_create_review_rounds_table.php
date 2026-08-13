<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('articles')->cascadeOnDelete();
            $table->unsignedInteger('round_number');
            $table->enum('blind_mode', ['single_blind', 'double_blind', 'open'])->default('single_blind');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['submission_id', 'round_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_rounds');
    }
};
