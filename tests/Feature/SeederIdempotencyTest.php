<?php

namespace Tests\Feature;

use App\Models\Navigation;
use Database\Seeders\NavigationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Pastikan seeder RBAC & navigasi IDEMPOTENT (aman dijalankan ulang
 * tanpa duplikasi — masalah lama dari RolePermissionSeeder versi awal).
 */
class SeederIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permission_seeder_can_run_twice_without_duplicates(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $rolesAfterFirst = Role::count();
        $permissionsAfterFirst = Permission::count();

        $this->seed(RolePermissionSeeder::class);

        $this->assertSame($rolesAfterFirst, Role::count());
        $this->assertSame($permissionsAfterFirst, Permission::count());

        // Role 'author' harus tetap punya semua permission submission.*
        $this->assertTrue(
            Role::findByName('author', 'web')->hasPermissionTo('submission.create')
        );
    }

    public function test_navigation_seeder_can_run_twice_without_duplicates(): void
    {
        $this->seed(NavigationSeeder::class);

        $sidebarParents = Navigation::where('location', 'sidebar')->whereNull('parent_id')->count();
        $headerParents = Navigation::where('location', 'header')->whereNull('parent_id')->count();

        $this->seed(NavigationSeeder::class);

        $this->assertSame($sidebarParents, Navigation::where('location', 'sidebar')->whereNull('parent_id')->count());
        $this->assertSame($headerParents, Navigation::where('location', 'header')->whereNull('parent_id')->count());
        $this->assertGreaterThan(0, $sidebarParents);
        $this->assertSame(6, $headerParents);
    }

    public function test_permission_driven_sidebar_built_from_seeder(): void
    {
        $this->seed([RolePermissionSeeder::class, NavigationSeeder::class]);

        // Setiap child sidebar harus punya permission (agar menu terkontrol).
        $sidebarItems = Navigation::where('location', 'sidebar')->whereNotNull('parent_id')->get();
        $this->assertTrue($sidebarItems->count() > 0);
        $this->assertTrue($sidebarItems->every(fn ($item) => $item->permission !== null));
    }
}