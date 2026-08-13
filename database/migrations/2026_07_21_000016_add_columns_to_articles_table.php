<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('tracking_code')->unique()->nullable();
            $table->string('current_stage')->default('draft');
            $table->string('section')->nullable();
            $table->text('funding_statement')->nullable();
            $table->text('conflict_of_interest')->nullable();
            $table->text('ethics_statement')->nullable();
            $table->text('acknowledgement')->nullable();
            $table->enum('license', ['cc-by', 'cc-by-nc', 'cc-by-nc-nd', 'cc-by-sa', 'all-rights-reserved'])->default('cc-by');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_code',
                'current_stage',
                'section',
                'funding_statement',
                'conflict_of_interest',
                'ethics_statement',
                'acknowledgement',
                'license',
            ]);
        });
    }
};
