<?php

namespace Tests\Unit;

use App\Models\VkAds;
use PHPUnit\Framework\TestCase;

class GetBannerStatisticsTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $vkAds = new VkAds();
        $stat = $vkAds->getBannerStatistics(153875793,'2023-02-29');
        echo var_dump($stat);
        $this->assertTrue(true);
    }
}
