<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetBannerStatisticsApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_banner_statistics_by_api(): void
    {
        $token =  DB::table('users')->where('name', 'admin')->value('api_token');
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->get('/api/getBannerStatistics?');

        $response->assertStatus(200)->assertJson(fn (AssertableJson $json) =>
        $json->has('count')->etc()
        );;
    }
}
