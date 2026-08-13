<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable journal_id to roles (for future multi-journal use)
        if (!Schema::hasColumn('roles', 'journal_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('journal_id')->nullable()->after('id');
                $table->foreign('journal_id')->references('id')->on('journals')->nullOnDelete();
            });
        }

        // Add nullable journal_id to model_has_roles (informational, not enforced FK when teams=false)
        if (!Schema::hasColumn('model_has_roles', 'journal_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('journal_id')->nullable()->after('role_id')->index();
            });
        }

        // Add nullable journal_id to model_has_permissions
        if (!Schema::hasColumn('model_has_permissions', 'journal_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('journal_id')->nullable()->after('permission_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropForeign('model_has_permissions_permission_id_foreign');
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
            $table->dropColumn('journal_id');
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropForeign('model_has_roles_role_id_foreign');
            $table->dropPrimary('model_has_roles_role_model_type_primary');
            $table->dropColumn('journal_id');
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });

        if (Schema::hasColumn('roles', 'journal_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropForeign(['journal_id']);
                $table->dropUnique('roles_journal_id_name_guard_name_unique');
                $table->dropColumn('journal_id');
                $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
            });
        }
    }
};
