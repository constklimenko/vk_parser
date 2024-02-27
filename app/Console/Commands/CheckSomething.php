<?php

namespace App\Console\Commands;

use App\Models\VkAds;
use App\Models\VkBanner;
use App\Models\VkBannerStat;
use App\Service\BearerToken;
use App\Service\VkAdsParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckSomething extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-something';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo BearerToken::getSampleAdmin();
    }
}
