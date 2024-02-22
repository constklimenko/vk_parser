<?php

namespace App\Console\Commands;

use App\Service\VkAdsParser;
use Illuminate\Console\Command;

class CheckParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-parser';

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
        $parser = new VkAdsParser();
        $parser->parse();
    }
}
