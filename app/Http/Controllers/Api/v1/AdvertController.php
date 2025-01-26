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
     * Display a listing of the resource.
     */
    public function index()
    {
        $advertsPrices = $this->advertService->getSubscribedAdvertsPrices();
        return AdvertResource::collection($advertsPrices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function subscribe(SubscribeAdvertRequest $request)
    {
        $this->advertService->subscribe($request);
        return response(['message' => 'Subscribed.'], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Advert $advert)
    {
        $prices = $this->advertService->getAdvertWithPrices($advert);
        return response(new AdvertResource($prices));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
