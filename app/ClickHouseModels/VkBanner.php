<?php

namespace App\ClickHouseModels;

use App\ClickHouseModels\Model;

class VkBanner extends Model
{
    public function __construct()
    {
        $this->table = 'vk_banners';
        parent::__construct();
    }

}
