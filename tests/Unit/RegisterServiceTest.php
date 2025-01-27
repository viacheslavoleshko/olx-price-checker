<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterRequest;
use App\UseCases\Services\RegisterService;



class RegisterServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        User::query()->delete();
        parent::tearDown();
    }

    public function test_register_creates_user()
    {
        // Arrange
        $request = new RegisterRequest([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $registerService = new RegisterService();

        // Act
        $createdUser = $registerService->register($request);

        // Assert
        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_register_uses_transaction()
    {
        // Arrange
        $request = new RegisterRequest([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $registerService = new RegisterService();

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) use ($request) {
            return $callback();
        });

        // Act
        $createdUser = $registerService->register($request);

        // Assert
        $this->assertInstanceOf(User::class, $createdUser);
    }
}