<?php

namespace App\Console\Commands;

use App\Models\VkBannerStat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckBanner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-banner';

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
        $banner = VkBannerStat::getRow(444);
        if ($banner) {
            Log::info('Пришел баннер: ' . $banner->name, $banner->toArray());
        }
    }
}
