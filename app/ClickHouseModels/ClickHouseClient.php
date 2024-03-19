<?php

namespace App\ClickHouseModels;

use ClickHouseDB\Client;

class ClickHouseClient extends Client
{
    public function __construct()
    {
        parent::__construct([
            'host'     => env( 'CLICKHOUSE_HOST', 'clickhouse' ) ,
            'port'     => env( 'CLICKHOUSE_PORT', '8123' ) ,
            'username' => env( 'CLICKHOUSE_USERNAME', 'default'),
            'password' => env( 'CLICKHOUSE_PASSWORD', ''),
            'settings' => [
                'timeout' => 10000,
            ],
        ]);
    }
}
