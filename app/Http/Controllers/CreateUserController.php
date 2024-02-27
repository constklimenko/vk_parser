<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\BearerToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateUserController extends Controller
{
    public function create(Request $request){
        $arrRequest = $request->all();
        $token = BearerToken::getFromRequest($request);
        $is_admin = User::isAdminByToken($token);
        if(!$is_admin) {
            return json_encode([
                "error" => "Not admin"
            ]);
        }
        $newToken = Str::random(40);
        $user = User::create([
            'name'      => $arrRequest['name'],
            'api_token' => $newToken,
            'is_admin'  => (bool)$arrRequest['admin']
        ]);
        return json_encode([
            "name" => $arrRequest['name'],
            "token" => $newToken,
            'id' => $user->id,
            'admin' => (bool)$arrRequest['admin']
        ]);
    }
}
