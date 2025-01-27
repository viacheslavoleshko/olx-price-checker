<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Models\Advert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\CheckUrlPrice;
use App\Jobs\SendPriceUpdateEmails;
use Illuminate\Support\Facades\Queue;
use App\UseCases\Services\ApiAdvertProvider;
use App\UseCases\Services\WebPageAdvertProvider;


class CheckUrlPriceTest extends TestCase
{
    protected function tearDown(): void
    {
        Advert::query()->delete();
        User::query()->delete();
        parent::tearDown();
    }

    public function test_handle_updates_advert_and_sends_emails()
    {
        Queue::fake();
        $user = User::factory()->create();
        $advert = Advert::factory()->create();
        $advert->users()->attach($user->id);

        $advertData = new \App\UseCases\Services\DataTransferObjects\AdvertData(
            'New Title',
            'http://new-url.com',
            100,
            'USD',
            false,
            false,
            false
        );

        $apiAdvertProvider = $this->createMock(ApiAdvertProvider::class);
        $apiAdvertProvider->method('getAdvertData')->willReturn($advertData);

        $webPageAdvertProvider = $this->createMock(WebPageAdvertProvider::class);
        $webPageAdvertProvider->method('getAdvertData')->willReturn(null);

        $job = new CheckUrlPrice($advert);
        $job->handle($apiAdvertProvider, $webPageAdvertProvider);

        $this->assertDatabaseHas('adverts', [
            'id' => $advert->id,
            'title' => 'New Title',
            'url' => 'http://new-url.com',
        ]);

        $this->assertDatabaseHas('prices', [
            'advert_id' => $advert->id,
            'value' => 100,
            'currency' => 'USD',
        ]);

        Queue::assertPushed(SendPriceUpdateEmails::class, function ($job) use ($advert) {
            return $job->advert->id === $advert->id;
        });
    }

    public function test_handle_deactivates_advert_when_no_data_found()
    {
        $advert = Advert::factory()->create(['is_active' => true]);

        $apiAdvertProvider = $this->createMock(ApiAdvertProvider::class);
        $apiAdvertProvider->method('getAdvertData')->willReturn(null);

        $webPageAdvertProvider = $this->createMock(WebPageAdvertProvider::class);
        $webPageAdvertProvider->method('getAdvertData')->willReturn(null);

        Log::shouldReceive('error')->once();

        $job = new CheckUrlPrice($advert);
        $job->handle($apiAdvertProvider, $webPageAdvertProvider);

        $this->assertDatabaseHas('adverts', [
            'id' => $advert->id,
            'is_active' => false,
        ]);
    }
}