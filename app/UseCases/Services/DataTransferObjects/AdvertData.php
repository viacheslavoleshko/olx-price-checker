<?php

namespace App\UseCases\Services\DataTransferObjects;

class AdvertData
{
    public string $title;
    public string $url;
    public ?string $value;
    public ?string $currency;
    public bool $negotiable;
    public bool $trade;
    public bool $budget;

    public function __construct(string $title, string $url, ?string $value, ?string $currency, bool $negotiable, bool $trade, bool $budget)
    {
        $this->title = $title;
        $this->url = $url;
        $this->value = $value;
        $this->currency = $currency;
        $this->negotiable = $negotiable;
        $this->trade = $trade;
        $this->budget = $budget;
    }
}