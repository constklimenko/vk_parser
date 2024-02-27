<?php

namespace App\Service;

use App\Models\User;

class BearerToken
{
    public static function getFromRequest($request):string | false
    {
        $token = $request->header('Authorization');
        if($token){
            return str_replace('Bearer ','', $token);
        }
        return false;
    }
    public static function getSample():string
    {
        return User::first()->api_token;
    }

    public static function getSampleAdmin():string
    {
        return User::where('is_admin', true)->first()->api_token;
    }

}
