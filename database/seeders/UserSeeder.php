<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'api_token' => env('ADMIN_API_TOKEN', Str::random(60)),
            'updated_at' => now(),
            'created_at' => now(),
            'is_admin' => true
        ]);
    }
}
