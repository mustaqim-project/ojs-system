<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $authorRole = Role::where('name', 'author')->first();
        if (! $authorRole) {
            return;
        }

        User::where('role', 'author')
            ->whereDoesntHave('roles')
            ->chunkById(100, function ($users) use ($authorRole) {
                foreach ($users as $user) {
                    $user->assignRole($authorRole);
                }
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Tidak perlu menghapus role yang sudah disinkronkan
    }
};
