<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;



class AuthControllerTest extends TestCase
{

    protected function tearDown(): void
    {
        User::query()->delete();
        parent::tearDown();
    }
    public function test_register()
    {
        Event::fake();

        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user' => [
                         'id', 'email', 'created_at', 'updated_at'
                     ],
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(202)
                 ->assertJsonStructure([
                     'user' => [
                         'id', 'email', 'created_at', 'updated_at'
                     ],
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Invalid credentials.',
                 ]);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(202)
                 ->assertJson([
                     'message' => 'Successfully logged out.',
                 ]);
    }
}