<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VkBannerStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vk_banner_stats')->insert([
            'banner_id' => 153875793,
            'shows' => 0,
            'clicks' => 0,
            'spent' => 0,
            'leads' => 0,
            'date' => '2023-02-28',
        ]);
        DB::table('vk_banner_stats')->insert([
            'banner_id' => 153875793,
            'shows' => 0,
            'clicks' => 0,
            'spent' => 0,
            'leads' => 0,
            'date' => '2023-02-29',
        ]);
        DB::table('vk_banner_stats')->insert([
            'banner_id' => 153875793,
            'shows' => 0,
            'clicks' => 0,
            'spent' => 0,
            'leads' => 0,
            'date' => '2023-02-30',
        ]);
    }
}
