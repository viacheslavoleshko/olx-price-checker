<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use App\UseCases\Services\ApiAdvertProvider;
use App\UseCases\Services\WebPageAdvertProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiAdvertProvider::class, function ($app) {
            return new ApiAdvertProvider();
        });

        $this->app->singleton(WebPageAdvertProvider::class, function ($app) {
            $client = new Client();
            return new WebPageAdvertProvider($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
