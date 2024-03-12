<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VkBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vk_banners')->insert([
            'banner_id' => 153875793,
            'campaign_id' => 90353461,
            'group_id' => 90353461,
            'name' => 'Лянтор (новые макеты)',
            'city' => 'Когалым',
            'section' => 'Вторичка',
            'subsection' => 'Общий',
            'status'=>'active'
        ]);

        DB::table('vk_banners')->insert([
            'banner_id' => 153875794,
            'campaign_id' => 90353461,
            'group_id' => 90353461,
            'name' => 'Лянтор (новые макеты)',
            'city' => 'Когалым',
            'section' => 'Вторичка',
            'subsection' => 'Общий',
            'status'=>'active'
        ]);

        DB::table('vk_banners')->insert([
            'banner_id' => 153875795,
            'campaign_id' => 90353461,
            'group_id' => 90353461,
            'name' => 'Лянтор (новые макеты)',
            'city' => 'Когалым',
            'section' => 'Вторичка',
            'subsection' => 'Общий',
            'status'=>'active'
        ]);
    }
}
