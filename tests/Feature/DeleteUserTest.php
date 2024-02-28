<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_delete_user(): void
    {
        $api_token = DB::table('users')->where('name', 'test_creating_user')->value('api_token');
        $this->artisan('app:delete-user '.$api_token);
        $exists = DB::table('users')->where('name', 'test_creating_user')->exists();
        $this->assertFalse($exists);
    }
}
