<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Advert;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendPriceUpdateEmails;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\UseCases\Services\ApiAdvertProvider;
use App\UseCases\Services\WebPageAdvertProvider;

class CheckUrlPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public Advert $advert;

    /**
     * Create a new job instance.
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
    }

    /**
     * Execute the job.
     */
    public function handle(ApiAdvertProvider $apiAdvertProvider, WebPageAdvertProvider $webPageAdvertProvider): void
    {
        $advertData = $apiAdvertProvider->getAdvertData($this->advert->url);

        if (empty($advertData)) {
            $advertData = $webPageAdvertProvider->getAdvertData($this->advert->url);
        }

        if ($advertData) {
            if ($this->advert->title != $advertData->title || $this->advert->url != $advertData->url) {
                DB::transaction(function () use ($advertData) {
                    $this->advert->update([
                        'title' => $advertData->title,
                        'url' => $advertData->url,
                    ]);
                });
            }
    
            $latestPrice = $this->advert->prices()->latest('created_at')->first();
    
            if(!$latestPrice || $latestPrice->value != $advertData->value) {
                DB::transaction(function () use ($advertData) {
                    $this->advert->prices()->create([
                        'value' => $advertData->value,
                        'currency' => $advertData->currency,
                        'negotiable' => $advertData->negotiable,
                        'trade' => $advertData->trade,
                        'budget' => $advertData->budget,
                    ]);
                });
    
                User::subscribers($this->advert->id)->chunk(100, function ($users) {
                    $emails = $users->pluck('email')->toArray();
                    dispatch(new SendPriceUpdateEmails($this->advert, $emails));
                });


            }
        } else {
            DB::transaction(function () {
            	$this->advert->update([
                    'is_active' => false,
                ]);
            });
            Log::error("Advert data not found for advert with ID: {$this->advert->id}");
        }
    }

    public function extractAdvertIdFromUrl($url)
    {
        preg_match('/\/obyavlenie\/[\w\-]+-(ID[\w]+)\.html/', $url, $matches);
        return $matches[1] ?? null;
    }
}
