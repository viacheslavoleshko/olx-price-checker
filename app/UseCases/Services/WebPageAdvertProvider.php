<?php

namespace App\UseCases\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\UseCases\Services\DataTransferObjects\AdvertData;

class WebPageAdvertProvider implements AdvertProviderInterface
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetches advert data from the given URL.
     *
     * @param string $url The URL of the advert page.
     * @return AdvertData|null The advert data or null if not found.
     */
    public function getAdvertData($url): ?AdvertData
    {
        $response = $this->client->get($url);
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        $titleElement = $crawler->filter('div[data-testid="ad_title"] h4');
        $title = $titleElement->count() ? $titleElement->text() : null;

        $priceElement = $crawler->filter('div[data-testid="ad-price-container"] h3');
        $priceText = $priceElement->count() ? $priceElement->text() : null;
        if($priceText) {
            $price = $this->parsePrice($priceText);
            $negotiableElement = $crawler->filter('div[data-testid="ad-price-container"] p');
            $negotiable = $negotiableElement->count() > 0;

            return new AdvertData(
                $title, 
                $url, 
                $price['value'], 
                $price['currency'], 
                $negotiable, 
                $price['trade'], 
                $price['budget']
            );
        }
        
        return null;
    }

    private function parsePrice(string $priceText): array
    {
        
        preg_match('/[\d\s,]+(?:\.\d+)?/', $priceText, $valueMatch);
        if (isset($valueMatch[0])) {
            $value = floatval(str_replace([' ', ','], ['', '.'], trim($valueMatch[0])));

            preg_match('/[^\d\s,.\-]+/', $priceText, $currencyMatch);
            $currency = isset($currencyMatch[0]) ? trim($currencyMatch[0]) : null;

            return [
                'value' => $value,
                'currency' => $currency,
                'trade' => false,
                'budget' => false,
            ];
        } else {
            switch (mb_strtolower($priceText)) {
                case 'обмен':
                case 'обмін':
                    return [
                        'value' => null,
                        'currency' => null,
                        'trade' => true,
                        'budget' => false
                    ];
                case 'бесплатно':
                case 'безкоштовно':
                    return [
                        'value' => 0,
                        'currency' => null,
                        'trade' => false,
                        'budget' => true
                    ];
                default:
                    return [
                        'value' => null,
                        'currency' => null,
                        'trade' => false,
                        'budget' => false
                    ];
            }
        }
    }
}