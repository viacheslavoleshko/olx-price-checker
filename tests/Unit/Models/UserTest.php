<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Advert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;


class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_register_a_user()
    {
        $email = 'test@example.com';
        $password = 'password';

        $user = User::register($email, $password);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $this->assertTrue(\Hash::check($password, $user->password));
    }

    #[Test]
    public function it_can_scope_subscribers()
    {
        $advert = Advert::factory()->create();
        $user = User::factory()->create();
        $user->adverts()->attach($advert->id);

        $subscribers = User::subscribers($advert->id)->get();

        $this->assertTrue($subscribers->contains($user));
    }
}