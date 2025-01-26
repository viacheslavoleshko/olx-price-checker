<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Advert;
use App\Jobs\SendPriceUpdateEmails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckUrlPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public string $baseUrl;
    public string $token;
    public Advert $advert;
    /**
     * Create a new job instance.
     */
    public function __construct(Advert $advert)
    {
        $this->baseUrl = config('services.olx.base_url') ?? '';
        $this->token = config('services.olx.token') ?? '';
        $this->advert = $advert;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $advertId = $this->extractAdvertIdFromUrl($this->advert->url);
    
        $endpointUrl = config('services.olx.get_advert_endpoint')($this->baseUrl, $advertId);
        $response = Http::acceptJson()->withToken($this->token)->get($endpointUrl);
        
        if($this->advert->title === null || $this->advert->title != $response->json()['data']['title'] || $this->advert->url != $response->json()['data']['url']) {
            $this->advert->update([
                'title' => $response->json()['data']['title'],
                'url' => $response->json()['data']['url'],
            ]);
        }

        $price = $response->json()['data']['price'];

        $latestPrice = $this->advert->prices()->latest('created_at')->first();


        if($latestPrice === null || $latestPrice->value != $price['value']) {
            $this->advert->prices()->create([
                'value' => $price['value'],
                'currency' => $price['currency'],
                'negotiable' => $price['negotiable'],
                'trade' => $price['trade'],
                'budget' => $price['budget'],
            ]);

            User::subscribers($this->advert->id)->chunk(100, function ($users) {
                $emails = $users->pluck('email')->toArray();
                dispatch(new SendPriceUpdateEmails($this->advert, $emails));
            });
        }
    }

    public function extractAdvertIdFromUrl($url)
    {
        preg_match('/\/obyavlenie\/[\w\-]+-(ID[\w]+)\.html/', $url, $matches);
        return $matches[1] ?? null;
    }
}
