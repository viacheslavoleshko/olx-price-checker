<?php

namespace App\Jobs;

use App\Models\Advert;
use App\Mail\PriceUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPriceUpdateEmails implements ShouldQueue
{
    use Queueable;

    public Advert $advert;
    public array $emails;
    /**
     * Create a new job instance.
     */
    public function __construct(Advert $advert, array $emails)
    {
        $this->advert = $advert;
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->emails)->queue(new PriceUpdated($this->advert));
    }
}
