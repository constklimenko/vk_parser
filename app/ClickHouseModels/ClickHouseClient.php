<?php

namespace App\ClickHouseModels;

use ClickHouseDB\Client;

class ClickHouseClient extends Client
{
    public function __construct()
    {
        parent::__construct([
            'host'     => config('database.connections.clickhouse.host'),
            'port'     => config('database.connections.clickhouse.port'),
            'username' => config('database.connections.clickhouse.username'),
            'password' => config('database.connections.clickhouse.password'),
            'settings' => [
                'timeout' => 10000,
            ],
        ]);
    }
}
