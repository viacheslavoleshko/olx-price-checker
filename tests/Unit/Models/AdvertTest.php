<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Advert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AdvertTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_scope_subscribed_adverts()
    {
        $user = User::factory()->create();
        $advert1 = Advert::factory()->create();
        $advert2 = Advert::factory()->create();

        $advert1->users()->attach($user->id);

        $subscribedAdverts = Advert::subscribedAdverts($user->id)->get();

        $this->assertTrue($subscribedAdverts->contains($advert1));
        $this->assertFalse($subscribedAdverts->contains($advert2));
    }
}