<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel settings - konfigurasi sistem (key-value store)
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('group')->default('general'); // Pengelompokan setting
            $table->string('label')->nullable(); // Label untuk UI
            $table->string('type')->default('text'); // text, number, boolean, textarea
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
            $table->index('group');
            $table->unique(['journal_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
