<?php

namespace App\Http\Controllers;

use App\Providers\ParsingVk;

class ParsingAddsController extends Controller
{
    public function parse()
    {
        event( new ParsingVk());
        return response('Ok', 200)->header('Content-Type', 'text/plain');
    }
}
