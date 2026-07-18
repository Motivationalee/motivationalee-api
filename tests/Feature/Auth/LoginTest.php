<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportSeeder::class);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('expires_in', 86400)
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonMissingPath('user.password');
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_refresh_access_token(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertOk();

        $response = $this->postJson('/api/refresh', [
            'refresh_token' => $login->json('refresh_token'),
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('expires_in', 86400);

        $this->assertNotSame(
            $login->json('access_token'),
            $response->json('access_token')
        );
    }

    public function test_user_cannot_refresh_with_invalid_token(): void
    {
        $this->postJson('/api/refresh', [
            'refresh_token' => 'invalid-refresh-token',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid or expired refresh token.');
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('access_token');

        Auth::forgetGuards();

        $response = $this->withToken($token)->getJson('/api/me');

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertOk();

        $response = $this->withToken($login->json('access_token'))->postJson('/api/logout');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        Auth::forgetGuards();

        $this->withToken($login->json('access_token'))
            ->getJson('/api/me')
            ->assertUnauthorized();

        $this->postJson('/api/refresh', [
            'refresh_token' => $login->json('refresh_token'),
        ])->assertUnauthorized();
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->postJson('/api/logout')->assertUnauthorized();
    }
}