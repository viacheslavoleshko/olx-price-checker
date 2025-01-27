<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\SendPriceUpdateEmails;
use App\Models\Advert;
use App\Mail\PriceUpdated;
use Illuminate\Support\Facades\Mail;


class SendPriceUpdateEmailsTest extends TestCase
{

    protected function tearDown(): void
    {
        Advert::query()->delete();
        parent::tearDown();
    }

    public function test_handle_sends_email_to_specified_addresses()
    {
        Mail::fake();

        $advert = Advert::factory()->create();
        $advert->prices()->create([
            'price' => 100,
            'currency' => 'USD',
        ]);
        $emails = ['test1@example.com', 'test2@example.com'];

        $job = new SendPriceUpdateEmails($advert, $emails);
        $job->handle();

        Mail::assertQueued(PriceUpdated::class, function ($mail) use ($emails, $advert) {
            return $mail->hasTo($emails) && $mail->advert->is($advert);
        });
    }
}