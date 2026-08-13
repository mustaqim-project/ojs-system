<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->unsignedInteger('year');
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['journal_id', 'number', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volumes');
    }
};
