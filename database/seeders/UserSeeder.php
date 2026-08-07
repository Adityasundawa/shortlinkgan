<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedPassword = env('SEED_USER_PASSWORD');

        if (! $seedPassword) {
            $this->command?->warn('SEED_USER_PASSWORD belum di-set; UserSeeder dilewati agar tidak membuat password default.');

            return;
        }

        $users = [
            [
                'id_team' => 0,
                'name' => 'Administrator',
                'username' => 'administrator',
                'user_label' => 'ADMIN',
                'email' => 'admin@huft.xyz',
                'role' => 'admin',
            ],
            [
                'id_team' => 1,
                'name' => 'Bombom',
                'username' => 'bombom',
                'user_label' => 'BBM',
                'email' => 'bombom@huft.xyz',
                'role' => 'user',
            ],
            [
                'id_team' => 2,
                'name' => 'Dustin RnD',
                'username' => 'dustin',
                'user_label' => 'DSTN',
                'email' => 'dustin@huft.xyz',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'id_team' => $user['id_team'],
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'user_label' => $user['user_label'],
                    'role' => $user['role'],
                    'password' => Hash::make($seedPassword),
                ]
            );
        }
    }
}
