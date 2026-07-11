<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel journals - representasi jurnal ilmiah
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('abbreviation')->nullable(); // Singkatan jurnal
            $table->text('description')->nullable();
            $table->string('issn_print', 20)->nullable();
            $table->string('issn_online', 20)->nullable();
            $table->string('cover_image')->nullable();
            $table->string('publisher')->nullable();
            $table->string('subject_area')->nullable();
            $table->enum('frequency', ['monthly', 'bimonthly', 'quarterly', 'semiannual', 'annual'])
                ->default('quarterly');
            $table->boolean('is_active')->default(true);
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
