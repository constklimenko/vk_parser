<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{
    public function delete(Request $request){
        $arrRequest = $request->all();
        $api_token = $arrRequest['api_token'];
        User::where('api_token', $api_token)->delete();
        return json_encode(['success' => true]);
    }
}
