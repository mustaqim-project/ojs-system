<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();
            $table->string('avatar_path')->nullable();
            $table->text('research_interest')->nullable();
            $table->string('scopus_id')->nullable();
            $table->string('google_scholar_url')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->unsignedBigInteger('institution_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_token',
                'avatar_path',
                'research_interest',
                'scopus_id',
                'google_scholar_url',
                'two_factor_enabled',
                'two_factor_secret',
                'institution_id',
            ]);
        });
    }
};
