<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Advert;
use App\UseCases\Services\AdvertService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdvertControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->advertService = $this->createMock(AdvertService::class);
    }

    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/advert/prices');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testSubscribe()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/advert/subscribe', [
            'url' => 'https://www.olx.ua/d/obyavlenie/test-advert-ID12345.html'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'Subscribed.']);
        $this->assertDatabaseHas('adverts', ['url' => 'https://www.olx.ua/d/obyavlenie/test-advert-ID12345.html']);
        $this->assertDatabaseHas('advert_user', ['user_id' => $user->id]);
    }

    // public function testShow()
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     $advert = Advert::factory()->create();
    //     $advert->prices()->create([
    //         'price' => 100,
    //         'currency' => 'USD',
    //     ]);

    //     $response = $this->getJson("/api/v1/advert/{$advert->id}/prices");
    //     $response->assertStatus(Response::HTTP_OK);
    // }
}