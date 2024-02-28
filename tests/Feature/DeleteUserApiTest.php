<?php

namespace Tests\Feature;

use App\Service\BearerToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteUserApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_delete_user(): void
    {
        $token =  DB::table('users')->where('name', 'Test')->value('api_token');
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->delete('/api/deleteUser?api_token=' . $token);
        $response->assertStatus(200)->assertJson(
            [
                'success' => true
            ]
        );
        $exists = DB::table('users')->where('name', 'Test')->exists();
        $this->assertFalse($exists);
    }
}
