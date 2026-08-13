<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Journal;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            InstitutionSeeder::class,
            UserSeeder::class,
            JournalSeeder::class,
            IssueSeeder::class,
            SettingSeeder::class,
            NavigationSeeder::class,
            ArticleSeeder::class,
            ApiIntegrationSeeder::class,
        ]);

        // Get or create default journal
        $journal = Journal::updateOrCreate(
            ['slug' => 'default-journal'],
            [
                'title' => 'Default Journal',
                'slug' => 'default-journal',
                'abbreviation' => 'DJ',
                'description' => 'Default journal for testing',
                'issn_print' => '0000-0000',
                'issn_online' => '0000-0001',
                'publisher' => 'Default Publisher',
                'subject_area' => 'General Science',
                'is_active' => true,
            ]
        );

        // Create super admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            // Set team context (journal_id) before assigning role — required when Spatie teams=true
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($journal->id);
            $admin->assignRole($superAdminRole);
        }

        // Create default settings
        $settings = [
            'journal_name' => 'Default Journal',
            'journal_email' => 'journal@example.com',
            'apc_amount' => '500000',
            'apc_currency' => 'IDR',
            'review_due_days' => '14',
            'invoice_due_days' => '14',
            'fonnte_api_key' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value, $journal->id);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Super Admin: admin@example.com / password');
    }
}
