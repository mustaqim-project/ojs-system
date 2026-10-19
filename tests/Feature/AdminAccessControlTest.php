<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\NavigationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Verifikasi arsitektur otorisasi permission-driven:
 *  - middleware admin.panel (gerbang panel admin)
 *  - middleware permission: per resource/action
 *  - role kustom di Admin Panel langsung memengaruhi akses.
 */
class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            NavigationSeeder::class,
        ]);
    }

    private function userWithRole(string $legacyRole, string $spatieRole): User
    {
        $user = User::factory()->create(['role' => $legacyRole, 'is_active' => true]);
        $user->syncRoles([$spatieRole]);

        return $user;
    }

    public function test_guest_is_redirected_to_login_for_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_author_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->userWithRole('author', 'author'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_editor_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->userWithRole('editor', 'editor'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $response = $this->actingAs($this->userWithRole('admin', 'admin'))
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_legacy_admin_without_spatie_role_still_has_access(): void
    {
        $legacyAdmin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($legacyAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_custom_role_with_single_permission_gets_matching_access_only(): void
    {
        $role = Role::create(['name' => 'user-manager', 'guard_name' => 'web'])
            ->syncPermissions([Permission::findByName('users.view', 'web')]);

        $manager = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $manager->syncRoles(['user-manager']);

        // Mendapat akses ke users.* sesuai permission yang diberikan
        $this->actingAs($manager)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.users.create'))->assertForbidden();

        // Tapi tidak ke area lain tanpa permission
        $this->actingAs($manager)->get(route('admin.roles.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('admin.navigations.index'))->assertForbidden();
    }
}