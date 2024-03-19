<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ClickHouseModels\ClickHouseClient as Client;

class ClickhouseMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse-migrate';

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
        $client = new Client();

        $query = "CREATE TABLE IF NOT EXISTS vk_banners
        (
          `id` int ,
          `campaign_id` int ,
          `group_id` int ,
          `name` varchar(252),
          `city` varchar(252) ,
          `section` varchar(252) ,
          `subsection` varchar(252),
          `status` varchar(252)
        )
         ENGINE = ReplacingMergeTree()
         PRIMARY KEY (`id`)";

        $client->write($query);

        $query2 = "CREATE TABLE IF NOT EXISTS vk_banner_stats
        (
          `banner_id` Int32,
          `shows` Int32,
          `clicks` Int32,
          `leads` Int32,
          `date` Date,
          `spent` Float32
        )
        ENGINE = MergeTree()
        PRIMARY KEY (`banner_id`, `date`)";
        $client->write($query2);
    }
}
