<?php

use App\Events\ListUpdate;
use App\Events\UserLoggedIn;
use App\Facades\GameCache;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/insert', function(Request $request) {
    $data = $request->all();
    // Broadcast to others.
    broadcast(new ListUpdate($data));
});

