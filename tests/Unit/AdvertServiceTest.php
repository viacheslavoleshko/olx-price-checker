<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Advert;
use Illuminate\Support\Collection;
use App\UseCases\Services\AdvertService;
use App\Http\Requests\SubscribeAdvertRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AdvertServiceTest extends TestCase
{

    protected $advertService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->advertService = new AdvertService();
    }

    protected function tearDown(): void
    {
        Advert::query()->delete();
        User::query()->delete();
        parent::tearDown();
    }

    public function testSubscribe(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new SubscribeAdvertRequest([
            'url' => 'http://example.com/advert/1'
        ]);

        $this->advertService->subscribe($request);

        $this->assertDatabaseHas('adverts', ['url' => 'http://example.com/advert/1']);
        $this->assertDatabaseHas('advert_user', ['user_id' => $user->id]);
    }

    public function testGetSubscribedAdvertsPrices(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $advert = Advert::factory()->create();
        $advert->prices()->create([
            'price' => 100,
            'currency' => 'USD',
        ]);
        $user->adverts()->attach($advert->id);
        

        $prices = $this->advertService->getSubscribedAdvertsPrices();

        $this->assertInstanceOf(Collection::class, $prices);
        $this->assertCount(1, $prices);
        $this->assertCount(1, $advert->prices);
    }

    public function testGetAdvertWithPrices(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $advert = Advert::factory()->create();
        $user->adverts()->attach($advert->id);

        $result = $this->advertService->getAdvertWithPrices($advert);

        $this->assertInstanceOf(Advert::class, $result);
        $this->assertEquals($advert->id, $result->id);
    }

    public function testGetAdvertWithPricesThrowsException(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $advert = Advert::factory()->create();

        $this->advertService->getAdvertWithPrices($advert);
    }
}