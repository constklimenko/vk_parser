<?php

namespace App\Providers;

use App\Models\SuccessParsingMessenger;
use App\Models\VkAds;
use App\Providers\ParsingVk;
use App\Service\VkAdsParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ParsingVkListener
{


    /**
     * Обработка события
     */
    public function handle(ParsingVk $event): void
    {
        Log::info('начало обработки');
        (new VkAdsParser())->parse();
        Log::info('конец обработки');
        //отправка сообщения о том, что парсинг успешно завершен
        (new SuccessParsingMessenger())->sendMessage(' end');
    }
}
