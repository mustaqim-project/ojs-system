<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('articles', 'sort_order')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('pages_end')->index();
            });
        }

        // Inisialisasi urutan artikel yang sudah ada (terbaru di atas)
        $ids = DB::table('articles')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id');

        foreach ($ids as $index => $id) {
            DB::table('articles')->where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('articles', 'sort_order')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropIndex(['sort_order']);
                $table->dropColumn('sort_order');
            });
        }
    }
};
