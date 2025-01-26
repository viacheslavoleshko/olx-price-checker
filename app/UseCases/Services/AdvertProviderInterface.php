<?php

namespace App\UseCases\Services;

use App\UseCases\Services\DataTransferObjects\AdvertData;

interface AdvertProviderInterface
{
    public function getAdvertData($url): ?AdvertData;
}