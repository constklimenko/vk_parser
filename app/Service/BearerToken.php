<?php

namespace App\Service;

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

}
