<?php

namespace App\Console\Commands;

use App\Models\VkAdsTokens;
use Illuminate\Console\Command;

class CheckTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tokens';

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
        $tokens = new VkAdsTokens();
    }
}
