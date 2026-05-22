<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_name_and_role(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret1234'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'name', 'role'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'role' => 'admin',
                ],
            ]);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
