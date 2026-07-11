<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'        => 'Administrator',
                'email'       => 'admin@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'affiliation' => 'OJS System',
                'is_active'   => true,
            ],
            [
                'name'        => 'Dr. Budi Santoso',
                'email'       => 'editor@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'editor',
                'affiliation' => 'Universitas Indonesia',
                'is_active'   => true,
            ],
            [
                'name'        => 'Prof. Siti Rahayu',
                'email'       => 'reviewer1@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'reviewer',
                'affiliation' => 'Institut Teknologi Bandung',
                'is_active'   => true,
            ],
            [
                'name'        => 'Dr. Ahmad Fauzi',
                'email'       => 'reviewer2@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'reviewer',
                'affiliation' => 'Universitas Gadjah Mada',
                'is_active'   => true,
            ],
            [
                'name'        => 'Rizky Pratama',
                'email'       => 'author@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'author',
                'affiliation' => 'Universitas Brawijaya',
                'is_active'   => true,
            ],
            [
                'name'        => 'Dewi Kurniawati',
                'email'       => 'author2@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'author',
                'affiliation' => 'Universitas Diponegoro',
                'is_active'   => true,
            ],
            [
                'name'        => 'Pembaca Umum',
                'email'       => 'reader@ojs.id',
                'password'    => Hash::make('password'),
                'role'        => 'reader',
                'affiliation' => null,
                'is_active'   => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('Users seeded! Login: admin@ojs.id / password');
    }
}
