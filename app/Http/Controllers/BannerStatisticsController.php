<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BannerStatisticsController extends Controller
{
    public function get( Request $request ) {
        return json_encode(
            [
                'count' => 10
            ]
        );
    }
}
