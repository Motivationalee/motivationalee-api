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
        User::query()->updateOrCreate(
            ['email' => 'admin@motivationalee.com'],
            [
                'name' => 'Admin',
                'password' => 'Default123',
                'email_verified_at' => now(),
            ],
        );
    }
}
