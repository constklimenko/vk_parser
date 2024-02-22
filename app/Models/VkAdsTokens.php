<?php

namespace App\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VkAdsTokens extends Model
{
    use HasFactory;

    protected $table = 'vk_ads_tokens';
    protected $access_token;
    protected $refresh_token;
    public string $ads_url;
    public Client $client;
    public bool $valid;

    public function __construct()
    {
        $this->ads_url = env('VK_ADS_URL');
        $this->valid = true;
        $this->client = new Client();

        $tokens = DB::select("SELECT access_token, refresh_token FROM vk_ads_tokens ORDER BY id DESC LIMIT 1");
        if(!empty($tokens)){
            $tokens = $tokens[0];
            $this->access_token = $tokens->access_token;
            $this->refresh_token = $tokens->refresh_token;
        }else{
            $this->refresh_token = env('VK_ADS_REFRESH_TOKEN');
            $this->refreshToken();
        }

        $status = $this->checkToken();

        if( $status == 'expired_token' ){
            if($this->refreshToken()){
                $status = $this->checkToken();
            }else{
                $status = 'error';
            }
        }

        if($status !== 'valid_token' ){
            $this->valid = false;
        }
    }

    /**
     * Проверяет токен, делая запрос к методу User
     * @return mixed|string
     */
    public function checkToken()
    {
        try {
            $response = $this->client->request('GET', $this->ads_url . 'v3/user.json', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->access_token,
                ]
            ]);
        } catch (GuzzleException $e) {
            return json_decode($e->getResponse()->getBody()->getContents(), true)['error']['code'];
        }
        return 'valid_token';
    }

    /**
     * Обновляет токены
     * @return bool
     */
    private function refreshToken(): bool
    {
        $data = [
            'client_id' => env('VK_ADS_CLIENT_ID'),
            'client_secret' => env('VK_ADS_CLIENT_SECRET'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refresh_token
        ];

        try {
            $response = $this->client->request('POST', $this->ads_url . 'v2/oauth2/token.json', [
                'form_params' => $data
            ]);
        } catch (GuzzleException $e) {
            Log::channel('tokens')->debug($e->getResponse()->getBody()->getContents());
            return false;
        }

        $responseArray = json_decode($response->getBody()->getContents(), true);

        $this->access_token = $responseArray['access_token'];
        $this->refresh_token = $responseArray['refresh_token'];

        return $this->refreshDB();
    }

    /**
     * Обновляет токены в базе данных
     * @return bool
     */
    private function refreshDB(): bool
    {
        $n = DB::update('UPDATE vk_ads_tokens SET access_token = ?, refresh_token = ? WHERE id = 1',
            [$this->access_token, $this->refresh_token]);
        if($n == 0){
            DB::insert('INSERT INTO vk_ads_tokens (access_token, refresh_token) VALUES (?, ?)',
                [$this->access_token, $this->refresh_token]);
            return true;
        }
        if($n == 1){
            return true;
        }
        return false;
    }

    public function getAccessToken(){
        return $this->access_token;
    }
}
