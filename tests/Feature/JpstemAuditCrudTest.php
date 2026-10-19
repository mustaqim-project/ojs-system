<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Navigation;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\NavigationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JpstemAuditCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $author;
    private User $editor;
    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            NavigationSeeder::class,
        ]);

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->admin->syncRoles(['super-admin']);

        $this->author = User::factory()->create(['role' => 'author', 'is_active' => true]);
        $this->author->syncRoles(['author']);

        $this->editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $this->editor->syncRoles(['editor']);

        $this->reviewer = User::factory()->create(['role' => 'reviewer', 'is_active' => true]);
        $this->reviewer->syncRoles(['reviewer']);
    }

    public function test_navigations_full_crud_and_toggle_active(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('admin.navigations.store'), [
            'title'    => 'Test Menu',
            'url'      => '/test-menu',
            'location' => 'header',
            'order'    => 10,
        ]);
        $response->assertRedirect(route('admin.navigations.index'));
        $nav = Navigation::where('title', 'Test Menu')->first();
        $this->assertNotNull($nav);

        // 2. Edit View (Verifies missing route admin.navigations.edit is fixed)
        $this->actingAs($this->admin)->get(route('admin.navigations.edit', $nav))
            ->assertOk()
            ->assertSee('Test Menu');

        // 3. Update
        $this->actingAs($this->admin)->put(route('admin.navigations.update', $nav), [
            'title'    => 'Test Menu Updated',
            'url'      => '/test-menu-updated',
            'location' => 'header',
            'order'    => 11,
        ])->assertRedirect(route('admin.navigations.index'));
        $nav->refresh();
        $this->assertEquals('Test Menu Updated', $nav->title);

        // 4. Toggle Active (Verifies missing route admin.navigations.toggle-active is fixed)
        $initialState = $nav->is_active;
        $this->actingAs($this->admin)->patch(route('admin.navigations.toggle-active', $nav))
            ->assertSessionHas('success');
        $nav->refresh();
        $this->assertNotEquals($initialState, $nav->is_active);

        // 5. Delete
        $this->actingAs($this->admin)->delete(route('admin.navigations.destroy', $nav))
            ->assertRedirect(route('admin.navigations.index'));
        $this->assertDatabaseMissing('navigations', ['id' => $nav->id]);
    }

    public function test_journals_full_crud_including_extra_fields_and_destroy(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('admin.journals.store'), [
            'title'        => 'Journal of Applied Computing',
            'abbreviation' => 'JAC',
            'issn_print'   => '1234-5678',
            'issn_online'  => '8765-4321',
            'publisher'    => 'Madani Publisher',
            'subject_area' => 'Computer Science',
            'frequency'    => 'quarterly',
            'description'  => 'Leading journal in applied computing.',
        ]);
        $response->assertRedirect(route('admin.journals.index'));
        $journal = Journal::where('title', 'Journal of Applied Computing')->first();
        $this->assertNotNull($journal);

        // 2. Edit View displays all fields
        $this->actingAs($this->admin)->get(route('admin.journals.edit', $journal))
            ->assertOk()
            ->assertSee('JAC')
            ->assertSee('1234-5678')
            ->assertSee('8765-4321')
            ->assertSee('Madani Publisher')
            ->assertSee('Computer Science');

        // 3. Update all 5 previously missing fields
        $this->actingAs($this->admin)->put(route('admin.journals.update', $journal), [
            'title'        => 'Journal of Applied Computing and AI',
            'abbreviation' => 'JACAI',
            'issn_print'   => '1111-2222',
            'issn_online'  => '3333-4444',
            'publisher'    => 'Global Madani Publisher',
            'subject_area' => 'Artificial Intelligence',
            'frequency'    => 'bimonthly',
            'description'  => 'Updated scope with AI.',
            'is_active'    => 1,
        ])->assertRedirect(route('admin.journals.index'));

        $journal->refresh();
        $this->assertEquals('Journal of Applied Computing and AI', $journal->title);
        $this->assertEquals('JACAI', $journal->abbreviation);
        $this->assertEquals('1111-2222', $journal->issn_print);
        $this->assertEquals('3333-4444', $journal->issn_online);
        $this->assertEquals('Global Madani Publisher', $journal->publisher);
        $this->assertEquals('Artificial Intelligence', $journal->subject_area);

        // 4. Destroy (soft delete)
        $this->actingAs($this->admin)->delete(route('admin.journals.destroy', $journal))
            ->assertRedirect(route('admin.journals.index'));
        $this->assertSoftDeleted('journals', ['id' => $journal->id]);
    }

    public function test_issues_full_crud_including_edit_update_destroy_publish(): void
    {
        $journal = Journal::factory()->create(['title' => 'Test Science Journal', 'is_active' => true]);

        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('admin.issues.store'), [
            'journal_id'  => $journal->id,
            'title'       => 'Vol. 1 No. 1 2026',
            'volume'      => 1,
            'number'      => 1,
            'year'        => 2026,
            'status'      => 'draft',
            'description' => 'Inaugural issue',
        ]);
        $response->assertRedirect(route('admin.issues.index'));
        $issue = Issue::where('title', 'Vol. 1 No. 1 2026')->first();
        $this->assertNotNull($issue);

        // 2. Edit View
        $this->actingAs($this->admin)->get(route('admin.issues.edit', $issue))
            ->assertOk()
            ->assertSee('Vol. 1 No. 1 2026');

        // 3. Update
        $this->actingAs($this->admin)->put(route('admin.issues.update', $issue), [
            'journal_id'  => $journal->id,
            'title'       => 'Vol. 1 No. 1 Jan-Mar 2026',
            'volume'      => 1,
            'number'      => 1,
            'year'        => 2026,
            'status'      => 'draft',
            'description' => 'Updated inaugural issue description',
        ])->assertRedirect(route('admin.issues.index'));

        $issue->refresh();
        $this->assertEquals('Vol. 1 No. 1 Jan-Mar 2026', $issue->title);

        // 4. Publish
        $this->actingAs($this->admin)->patch(route('admin.issues.publish', $issue))
            ->assertSessionHas('success');
        $issue->refresh();
        $this->assertEquals('published', $issue->status);

        // 5. Destroy
        $this->actingAs($this->admin)->delete(route('admin.issues.destroy', $issue))
            ->assertRedirect(route('admin.issues.index'));
        $this->assertSoftDeleted('issues', ['id' => $issue->id]);
    }

    public function test_users_crud_preserves_phone_and_supports_custom_roles(): void
    {
        // 1. Create a custom Spatie role
        Role::create(['name' => 'finance-officer', 'guard_name' => 'web']);

        // 2. Create user with phone and custom role
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'        => 'Finance Staff',
            'email'       => 'finance.staff@example.com',
            'password'    => 'Secret1234!',
            'role'        => 'finance-officer',
            'phone'       => '081234567890',
            'affiliation' => 'Madani University',
        ]);
        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'finance.staff@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('081234567890', $user->phone);
        $this->assertTrue($user->hasRole('finance-officer'));

        // 3. Edit view shows phone number
        $this->actingAs($this->admin)->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee('081234567890');

        // 4. Update user phone and ensure it is not overwritten with null
        $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name'        => 'Finance Staff Updated',
            'email'       => 'finance.staff@example.com',
            'role'        => 'finance-officer',
            'phone'       => '089876543210',
            'affiliation' => 'Madani University Updated',
            'is_active'   => 1,
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertEquals('089876543210', $user->phone);
        $this->assertEquals('Finance Staff Updated', $user->name);
    }

    public function test_roles_management_protects_system_role_name_while_allowing_permissions(): void
    {
        $superAdminRole = Role::findByName('super-admin', 'web');

        // 1. Attempt to change system role name is blocked
        $this->actingAs($this->admin)->put(route('admin.roles.update', $superAdminRole), [
            'name'        => 'renamed-system-role',
            'permissions' => ['dashboard.view'],
        ])->assertSessionHas('error');

        // 2. Updating permissions while keeping system role name succeeds
        $this->actingAs($this->admin)->put(route('admin.roles.update', $superAdminRole), [
            'name'        => 'super-admin',
            'permissions' => ['dashboard.view', 'users.view'],
        ])->assertRedirect(route('admin.roles.index'));

        // 3. Creating custom role with duplicate name is rejected
        $this->actingAs($this->admin)->post(route('admin.roles.store'), [
            'name' => 'super-admin',
        ])->assertSessionHasErrors(['name']);
    }

    public function test_payment_verification_and_rejection_for_waiting_verification_status(): void
    {
        $journal = Journal::factory()->create(['title' => 'JPSTEM', 'abbreviation' => 'JPSTEM']);
        $article = Article::create([
            'journal_id'      => $journal->id,
            'author_id'       => $this->author->id,
            'title'           => 'Test Research Paper',
            'slug'            => 'test-research-paper',
            'abstract'        => 'A test abstract for verification.',
            'manuscript_file' => 'manuscripts/sample.pdf',
            'status'          => 'accepted',
        ]);
        $invoice = Invoice::create([
            'journal_id'     => $journal->id,
            'submission_id'  => $article->id,
            'invoice_number' => 'INV-2026-001',
            'amount'         => 1500000,
            'currency'       => 'IDR',
            'due_date'       => now()->addDays(14),
            'status'         => 'waiting_payment',
        ]);
        $payment = Payment::create([
            'invoice_id'     => $invoice->id,
            'author_id'      => $this->author->id,
            'amount'         => 1500000,
            'payment_method' => 'bank_transfer',
            'payment_date'   => now(),
            'status'         => 'waiting_verification',
            'notes'          => 'Paid via BCA',
        ]);

        // 1. Verify Payment::with('article') Eloquent relation works without exception
        $loadedPayment = Payment::with(['article.author', 'article.journal'])->find($payment->id);
        $this->assertNotNull($loadedPayment->article);
        $this->assertEquals($article->id, $loadedPayment->article->id);

        // 2. Payment index loads and displays waiting_verification status
        $this->actingAs($this->admin)->get(route('admin.payments.index', ['status' => 'uploaded']))
            ->assertOk()
            ->assertSee($payment->invoice_code);

        // 3. Payment show page displays verification and rejection forms
        $this->actingAs($this->admin)->get(route('admin.payments.show', $payment))
            ->assertOk()
            ->assertSee('Setujui Pembayaran')
            ->assertSee('Tolak Pembayaran');

        // 4. Verify payment
        $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'admin_notes' => 'Pembayaran terverifikasi valid.',
        ])->assertRedirect(route('admin.payments.index'));

        $payment->refresh();
        $this->assertEquals('verified', $payment->status);
    }

    public function test_sidebar_filters_portal_links_preventing_route_bleeding(): void
    {
        // Author dashboard should NOT render admin.*, editor.*, or reviewer.* links
        $response = $this->actingAs($this->author)->get(route('author.dashboard'));
        $response->assertOk();
        $response->assertDontSee(route('admin.articles.index'));
        $response->assertDontSee(route('editor.articles.index'));
        $response->assertDontSee(route('reviewer.reviews.index'));

        // Admin dashboard should NOT render reviewer links if admin is not assigned as reviewer
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminResponse->assertOk();
        $adminResponse->assertDontSee(route('reviewer.reviews.index'));
    }
}
