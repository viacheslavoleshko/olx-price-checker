<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\UseCases\Services\ApiAdvertProvider;
use App\UseCases\Services\DataTransferObjects\AdvertData;
use Illuminate\Support\Facades\Http;



class ApiAdvertProviderTest extends TestCase
{
    protected ApiAdvertProvider $apiAdvertProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiAdvertProvider = new ApiAdvertProvider();
    }

    public function testGetAdvertDataReturnsAdvertData()
    {
        $url = 'https://www.olx.ua/obyavlenie/test-advert-ID12345.html';
        $advertId = 'ID12345';
        $endpointUrl = config('services.olx.get_advert_endpoint')($this->apiAdvertProvider->baseUrl, $advertId);

        $mockResponse = [
            'data' => [
                'title' => 'Test Advert',
                'url' => $url,
                'price' => [
                    'value' => 1000,
                    'currency' => 'USD',
                    'negotiable' => false,
                    'trade' => false,
                    'budget' => false,
                ],
            ],
        ];

        Http::fake([
            $endpointUrl => Http::response($mockResponse, 200),
        ]);

        $advertData = $this->apiAdvertProvider->getAdvertData($url);

        $this->assertInstanceOf(AdvertData::class, $advertData);
        $this->assertEquals('Test Advert', $advertData->title);
        $this->assertEquals($url, $advertData->url);
        $this->assertEquals(1000, $advertData->value);
        $this->assertEquals('USD', $advertData->currency);
        $this->assertFalse($advertData->negotiable);
        $this->assertFalse($advertData->trade);
        $this->assertFalse($advertData->budget);
    }

    public function testGetAdvertDataReturnsNullOnFailure()
    {
        $url = 'https://www.olx.ua/obyavlenie/test-advert-ID12345.html';
        $advertId = 'ID12345';
        $endpointUrl = config('services.olx.get_advert_endpoint')($this->apiAdvertProvider->baseUrl, $advertId);

        Http::fake([
            $endpointUrl => Http::response([], 404),
        ]);

        $advertData = $this->apiAdvertProvider->getAdvertData($url);

        $this->assertNull($advertData);
    }

    public function testExtractAdvertIdFromUrl()
    {
        $url = 'https://www.olx.ua/obyavlenie/test-advert-ID12345.html';
        $advertId = $this->apiAdvertProvider->extractAdvertIdFromUrl($url);

        $this->assertEquals('ID12345', $advertId);
    }

    public function testExtractAdvertIdFromUrlReturnsNullForInvalidUrl()
    {
        $url = 'https://www.olx.ua/obyavlenie/invalid-url.html';
        $advertId = $this->apiAdvertProvider->extractAdvertIdFromUrl($url);

        $this->assertNull($advertId);
    }
}