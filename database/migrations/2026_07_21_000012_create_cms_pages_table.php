<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('seo_meta')->nullable();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            $table->unique(['journal_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
