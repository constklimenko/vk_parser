<?php

namespace App\Providers;

use App\Service\VkAdsParser;

class ParsingVkListener
{


    /**
     * Обработка события
     */
    public function handle(ParsingVk $event): void
    {
        (new VkAdsParser())->parse();
    }
}
