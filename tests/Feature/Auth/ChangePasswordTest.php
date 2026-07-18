<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportSeeder::class);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('access_token');

        Auth::forgetGuards();

        $this->withToken($token)
            ->postJson('/api/change-password', [
                'current_password' => 'password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password changed successfully.');

        $this->assertTrue(
            Hash::check('NewPassword123!', User::where('email', 'test@example.com')->first()->password)
        );

        $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
        ])->assertOk();
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('access_token');

        Auth::forgetGuards();

        $this->withToken($token)
            ->postJson('/api/change-password', [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_guest_cannot_change_password(): void
    {
        $this->postJson('/api/change-password', [
            'current_password' => 'password',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertUnauthorized();
    }
}