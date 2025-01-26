<?php

namespace App\UseCases\Services;

use App\Models\Advert;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\SubscribeAdvertRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdvertService
{
    public function subscribe(SubscribeAdvertRequest $request)
    {
        $advert = Advert::firstOrCreate([
            'url' => $request->url,
        ]);

        auth()->user()->adverts()->syncWithoutDetaching($advert->id);
    }

    /**
     * Get paginated prices for subscribed adverts.
     *
     * @return array
     */
    public function getSubscribedAdvertsPrices(): Collection
    {
        $adverts = Advert::subscribedAdverts(auth()->id())->with('prices')->get();
        return $adverts;
    }

    /**
     * Get prices for a specified advert that the user is subscribed to.
     *
     * @param int $advertId
     * @return array
     */
    public function getAdvertWithPrices(Advert $advert): Advert
    {
        try {
            $subscribedAdvert = Advert::subscribedAdverts(auth()->id())
                ->with('prices')
                ->where('id', $advert->id)
                ->firstOrFail();

            return $subscribedAdvert;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("You are not subscribed on this advert.");
        }
    }
}
