<?php

namespace App\UseCases\Services;

use Illuminate\Support\Facades\Http;
use App\UseCases\Services\DataTransferObjects\AdvertData;

class ApiAdvertProvider implements AdvertProviderInterface
{
    public string $baseUrl;
    public string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.olx.base_url') ?? '';
        $this->token = config('services.olx.token') ?? '';
    }

    public function getAdvertData($url): ?AdvertData
    {
        $advertId = $this->extractAdvertIdFromUrl($url);
    
        $endpointUrl = config('services.olx.get_advert_endpoint')($this->baseUrl, $advertId);
        $response = Http::acceptJson()->withToken($this->token)->get($endpointUrl);

        if ($response->successful()) {
            $data = $response->json()['data'];
            return new AdvertData(
                $data['title'], 
                $data['url'], 
                $data['price']['value'], 
                $data['price']['currency'], 
                $data['price']['negotiable'], 
                $data['price']['trade'], 
                $data['price']['budget']
            );
        }

        return null;
    }

    public function extractAdvertIdFromUrl($url)
    {
        preg_match('/\/obyavlenie\/[\w\-]+-(ID[\w]+)\.html/', $url, $matches);
        return $matches[1] ?? null;
    }
}