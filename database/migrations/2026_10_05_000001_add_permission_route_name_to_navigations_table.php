<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perluas tabel `navigations` agar mendukung sidebar dinamis berbasis
     * permission (menu panel dashboard yang dikelola lewat Admin Panel).
     *
     *  - `permission`  : permission yang dibutuhkan untuk melihat item (null = semua user login)
     *  - `route_name`  : nama route (atau glob awalan seperti `admin.users*`) untuk
     *                    resolver URL + penanda menu aktif.
     */
    public function up(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            if (!Schema::hasColumn('navigations', 'permission')) {
                $table->string('permission')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('navigations', 'route_name')) {
                $table->string('route_name')->nullable()->after('permission');
            }
        });
    }

    public function down(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            if (Schema::hasColumn('navigations', 'permission')) {
                $table->dropColumn('permission');
            }
            if (Schema::hasColumn('navigations', 'route_name')) {
                $table->dropColumn('route_name');
            }
        });
    }
};