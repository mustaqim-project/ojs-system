<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // e.g. "about", "editorial-team"
            $table->string('title');                    // Judul halaman
            $table->longText('body')->nullable();       // Konten HTML utama
            $table->text('meta_description')->nullable();
            $table->json('extra')->nullable();          // Data tambahan per halaman
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_pages');
    }
};
