<?php

namespace Tests\Feature;

use App\Service\BearerToken;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateUserApiTest extends TestCase
{
    /**
     * Тестирование создания нового пользователя по API
     */
    public function test_create_user(): void
    {
        $token = BearerToken::getSampleAdmin();
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->get('/api/createUser?name=Test&admin=0');

        $response->assertStatus(200)->assertJson(fn (AssertableJson $json) =>
        $json->where('name', 'Test')->where('admin', false)->has('token')->etc()
        );
    }
}
