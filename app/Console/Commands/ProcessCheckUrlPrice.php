<?php

namespace App\Console\Commands;

use App\Models\Advert;
use App\Jobs\CheckUrlPrice;
use Illuminate\Console\Command;

class ProcessCheckUrlPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-check-url-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $adverts = Advert::all();
        foreach ($adverts as $advert) {
            CheckUrlPrice::dispatch($advert);
        }
    }
}
