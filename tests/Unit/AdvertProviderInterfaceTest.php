
<?php

use PHPUnit\Framework\TestCase;
use App\UseCases\Services\AdvertProviderInterface;
use App\UseCases\Services\DataTransferObjects\AdvertData;


class AdvertProviderInterfaceTest extends TestCase
{
    public function testGetAdvertDataReturnsAdvertData()
    {
        $mockAdvertProvider = $this->createMock(AdvertProviderInterface::class);
        $mockAdvertData = $this->createMock(AdvertData::class);
        
        $mockAdvertProvider->method('getAdvertData')
            ->willReturn($mockAdvertData);

        $this->assertInstanceOf(
            AdvertData::class,
            $mockAdvertProvider->getAdvertData('http://example.com')
        );
    }

    public function testGetAdvertDataReturnsNull()
    {
        $mockAdvertProvider = $this->createMock(AdvertProviderInterface::class);
        
        $mockAdvertProvider->method('getAdvertData')
            ->willReturn(null);

        $this->assertNull($mockAdvertProvider->getAdvertData('http://example.com'));
    }
}