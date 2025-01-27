<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    // public function test_email_already_verified()
    // {
    //     $user = User::factory()->create(['email_verified_at' => now()]);

    //     $hash = sha1($user->email);
    //     $response = $this->actingAs($user)->get(route('verification.verify', ['id' => $user->id, 'hash' => $hash]));

    //     $response->assertStatus(200);
    //     $response->assertJson(['message' => 'Verification already success']);
    // }

    // public function test_email_verification_success()
    // {
    //     Event::fake();

    //     $user = User::factory()->create(['email_verified_at' => null]);

    //     $hash = sha1($user->email);
    //     $response = $this->actingAs($user)->get(route('verification.verify', ['id' => $user->id, 'hash' => $hash]));

    //     $response->assertStatus(200);
    //     $response->assertJson(['message' => 'Verification success']);

    //     $this->assertNotNull($user->fresh()->email_verified_at);
    //     Event::assertDispatched(Verified::class);
    // }
}
