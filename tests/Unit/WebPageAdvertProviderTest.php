<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\UseCases\Services\WebPageAdvertProvider;
use App\UseCases\Services\DataTransferObjects\AdvertData;


class WebPageAdvertProviderTest extends TestCase
{
    public function testGetAdvertDataReturnsAdvertData()
    {
        $html = '<div data-testid="ad_title"><h4>Test Title</h4></div>
                 <div data-testid="ad-price-container"><h3>1000 USD</h3></div>';
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], $html));

        $provider = new WebPageAdvertProvider($client);
        $advertData = $provider->getAdvertData('http://example.com');

        $this->assertInstanceOf(AdvertData::class, $advertData);
        $this->assertEquals('Test Title', $advertData->title);
        $this->assertEquals('http://example.com', $advertData->url);
        $this->assertEquals(1000, $advertData->value);
        $this->assertEquals('USD', $advertData->currency);
        $this->assertFalse($advertData->negotiable);
        $this->assertFalse($advertData->trade);
        $this->assertFalse($advertData->budget);
    }

    public function testGetAdvertDataReturnsNullForInvalidHtml()
    {
        $html = '<div data-testid="ad_title"></div>';
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], $html));

        $provider = new WebPageAdvertProvider($client);
        $advertData = $provider->getAdvertData('http://example.com');

        $this->assertNull($advertData);
    }

    public function testGetAdvertDataHandlesTrade()
    {
        $html = '<div data-testid="ad_title"><h4>Test Title</h4></div>
                 <div data-testid="ad-price-container"><h3>Обмен</h3></div>';
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], $html));

        $provider = new WebPageAdvertProvider($client);
        $advertData = $provider->getAdvertData('http://example.com');

        $this->assertInstanceOf(AdvertData::class, $advertData);
        $this->assertTrue($advertData->trade);
        $this->assertFalse($advertData->budget);
    }

    public function testGetAdvertDataHandlesBudget()
    {
        $html = '<div data-testid="ad_title"><h4>Test Title</h4></div>
                 <div data-testid="ad-price-container"><h3>Бесплатно</h3></div>';
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], $html));

        $provider = new WebPageAdvertProvider($client);
        $advertData = $provider->getAdvertData('http://example.com');

        $this->assertInstanceOf(AdvertData::class, $advertData);
        $this->assertTrue($advertData->budget);
        $this->assertFalse($advertData->trade);
    }

    public function testGetAdvertDataHandlesNegotiable()
    {
        $html = '<div data-testid="ad_title"><h4>Test Title</h4></div>
                 <div data-testid="ad-price-container"><h3>1000 USD</h3><p>Договорная</p></div>';
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], $html));

        $provider = new WebPageAdvertProvider($client);
        $advertData = $provider->getAdvertData('http://example.com');

        $this->assertInstanceOf(AdvertData::class, $advertData);
        $this->assertTrue($advertData->negotiable);
        $this->assertFalse($advertData->trade);
        $this->assertFalse($advertData->budget);
    }
}