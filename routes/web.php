<?php

use App\Enums\GameStatus;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Facades\GameCache;
use App\Http\Controllers\CreateGameController;
use App\Http\Controllers\JoinGameController;
use App\Http\Controllers\StartGameController;
use App\Http\Controllers\WaitingRoomController;
use App\Models\Game;
use App\Models\Kalooki;
use App\Models\Player;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
      return Inertia::render('Landing', [
          'canLogin' => Route::has('login'),
          'canRegister' => Route::has('register'),
          'laravelVersion' => Application::VERSION,
          'phpVersion' => PHP_VERSION,
      ]);
});

Route::get('/dashboard', function () {
  return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/kalooki/create', CreateGameController::class)->middleware(['auth', 'verified'])->name('game.create');

Route::get('/kalooki/{game}', WaitingRoomController::class);

Route::get('/kalooki/join/{code}', JoinGameController::class)->name('kalooki.join')->middleware(['auth', 'verified']);

Route::post('/kalooki/{game}/start', StartGameController::class)->middleware(['auth', 'verified'])->name('game.start');

Route::get('/kalooki/{game}/play', function (Game $game) {
  $kalooki = GameCache::getGameState($game->id);
  return Inertia::render('Board', [
//    'gameId' => $game->id,
//    'code' => $game->code,
//    'players' => $game->players,
//    'inviteLink' => $game->invite_link,
//    'isCreator' => $game->created_by === auth()->user()->id,
//    'player' => $kalooki->players[0]->id === auth()->user()->id ? $kalooki->players[0] : $kalooki->players[1],
//    'opponent' => $kalooki->players[0]->id === auth()->user()->id ? $kalooki->players[1] : $kalooki->players[0],
//    'turn' => $kalooki->turn,
//    'started' => $kalooki->started,
//    'finished' => $kalooki->finished,
//    'winner' => $kalooki->winner,
  ]);
})->middleware(['auth', 'verified'])->name('game.play');

require __DIR__ . '/auth.php';
