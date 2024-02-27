<?php

use App\Http\Controllers\ParsingAddsController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\CreateUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth.api')->get('/parse', [ParsingAddsController::class, 'parse']);
Route::middleware('auth.api')->get('/createUser', [CreateUserController::class, 'create']);

