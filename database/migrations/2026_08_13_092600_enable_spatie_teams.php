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
        // 1. Modify roles table (only if journal_id doesn't exist yet)
        if (!Schema::hasColumn('roles', 'journal_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('journal_id')->nullable()->after('id');
                
                // Drop unique constraint
                $table->dropUnique('roles_name_guard_name_unique');
                
                // Add new unique constraint
                $table->unique(['journal_id', 'name', 'guard_name'], 'roles_journal_id_name_guard_name_unique');
                
                // Foreign key relation
                $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
            });
        }

        // 2. Modify model_has_roles table
        if (!Schema::hasColumn('model_has_roles', 'journal_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign('model_has_roles_role_id_foreign');
                
                // Drop old primary key
                $table->dropPrimary('model_has_roles_role_model_type_primary');
                
                // Add journal_id column
                $table->unsignedBigInteger('journal_id')->default(1)->after('role_id');
                
                // Add new primary key
                $table->primary(['journal_id', 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
                
                // Restore foreign key on role_id
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                
                // Foreign key relation on journal_id
                $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
            });
        }

        // 3. Modify model_has_permissions table
        if (!Schema::hasColumn('model_has_permissions', 'journal_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign('model_has_permissions_permission_id_foreign');
                
                // Drop old primary key
                $table->dropPrimary('model_has_permissions_permission_model_type_primary');
                
                // Add journal_id column
                $table->unsignedBigInteger('journal_id')->default(1)->after('permission_id');
                
                // Add new primary key
                $table->primary(['journal_id', 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
                
                // Restore foreign key on permission_id
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                
                // Foreign key relation on journal_id
                $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
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
