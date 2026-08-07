<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's default admin user.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Lee Montajes',
                'email' => 'admin@leemontajes.com.au',
                'password' => 'Default123'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@motivationalee.com',
                'password' => 'Default123'
            ]
        ];
        foreach($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
