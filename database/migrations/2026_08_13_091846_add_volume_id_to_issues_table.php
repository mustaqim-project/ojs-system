<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->foreignId('volume_id')
                ->nullable()
                ->after('journal_id')
                ->constrained('volumes')
                ->nullOnDelete();
        });

        // Migrate existing issues to volumes
        $issues = DB::table('issues')->get();
        foreach ($issues as $issue) {
            if ($issue->volume && $issue->year) {
                $volume = DB::table('volumes')
                    ->where('journal_id', $issue->journal_id)
                    ->where('number', $issue->volume)
                    ->where('year', $issue->year)
                    ->first();

                if (!$volume) {
                    $volumeId = DB::table('volumes')->insertGetId([
                        'journal_id' => $issue->journal_id,
                        'number' => $issue->volume,
                        'year' => $issue->year,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $volumeId = $volume->id;
                }

                DB::table('issues')
                    ->where('id', $issue->id)
                    ->update(['volume_id' => $volumeId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['volume_id']);
            $table->dropColumn('volume_id');
        });
    }
};
