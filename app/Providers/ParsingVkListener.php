<?php

namespace App\Providers;

use App\Jobs\ProcessParsing;
use Illuminate\Support\Facades\Log;

class ParsingVkListener
{


    /**
     * Обработка события
     */
    public function handle(ParsingVk $event): void
    {
        ProcessParsing::dispatch();
    }
}
