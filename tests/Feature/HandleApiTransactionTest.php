<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class HandleApiTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_commits_database_changes_on_successful_api_responses(): void
    {
        Route::middleware('api')->post('/api/test-transaction-commit', function () {
            User::factory()->create([
                'email' => 'should-commit@example.com',
            ]);

            return response()->json(['ok' => true]);
        });

        $this->postJson('/api/test-transaction-commit')
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'should-commit@example.com',
        ]);
    }

    public function test_it_rolls_back_database_changes_when_an_exception_is_thrown(): void
    {
        Route::middleware('api')->post('/api/test-transaction-rollback', function () {
            User::factory()->create([
                'email' => 'should-rollback@example.com',
            ]);

            throw new RuntimeException('Intentional failure');
        });

        $this->postJson('/api/test-transaction-rollback')
            ->assertServerError();

        $this->assertDatabaseMissing('users', [
            'email' => 'should-rollback@example.com',
        ]);
    }

    public function test_it_rolls_back_database_changes_on_error_responses(): void
    {
        Route::middleware('api')->post('/api/test-transaction-error-response', function () {
            User::factory()->create([
                'email' => 'should-not-persist@example.com',
            ]);

            return response()->json(['message' => 'Failed'], 422);
        });

        $this->postJson('/api/test-transaction-error-response')
            ->assertUnprocessable();

        $this->assertDatabaseMissing('users', [
            'email' => 'should-not-persist@example.com',
        ]);
    }
}
