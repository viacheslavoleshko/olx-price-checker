<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Advert;
use App\UseCases\Services\WebPageAdvertProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\UseCases\Services\AdvertService;
use App\Http\Requests\SubscribeAdvertRequest;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller
{
    public $advertService;

    public function __construct(AdvertService $advertService)
    {
        $this->advertService = $advertService;
    }

    /**
     * @lrd:start
     * Display a listing of the subscribed adverts prices.
     * @lrd:end
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $advertsPrices = $this->advertService->getSubscribedAdvertsPrices();
        return AdvertResource::collection($advertsPrices);
    }

    
    /**
     * @lrd:start
     * Subscribe to an advert.
     * @lrd:end
     *
     * @param \App\Http\Requests\SubscribeAdvertRequest $request The request instance containing subscription details.
     * @return \Illuminate\Http\Response
     */
    public function subscribe(SubscribeAdvertRequest $request): Response
    {
        $this->advertService->subscribe($request);
        return response(['message' => 'Subscribed.'], Response::HTTP_OK);
    }

    
    /**
     * @lrd:start
     * Display the specified advert along with its prices.
     * @lrd:end
     *
     * @param Advert $advert The advert instance to be displayed.
     * @return \Illuminate\Http\Response The response containing the advert with prices.
     */
    public function show(Advert $advert): Response
    {
        $prices = $this->advertService->getAdvertWithPrices($advert);
        return response(new AdvertResource($prices), Response::HTTP_OK);
    }
}
