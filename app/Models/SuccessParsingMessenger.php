<?php

namespace App\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessParsingMessenger extends Model
{
    use HasFactory;

    public function sendMessage(string $message = '')
    {
        $client = new Client();
        $url = 'https://dubai-chat-bot.data-etagi.ru/send_message';
        try {
            $r = $client->request('POST', $url, [
                'json' => [
                    "message"   => $message,
                    "user_uuid" => 'test_43',
                    "chat_id"   => 0
                ]

            ]);
        } catch (GuzzleException $e) {

        }
    }
}
