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
    'canLogin'       => Route::has('login'),
    'canRegister'    => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion'     => PHP_VERSION,
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
  $gameState = GameCache::getGameState(auth()->user()->id);
  $kalooki = $gameState['game'];
  // turn is random at start of game
  $players = $kalooki->players;
  $opponent = $players[array_search(auth()->user()->name, array_column($players, 'name')) === 0 ? 1 : 0]->name;
  $stock  = $kalooki->stock;
  $discard = $kalooki->discard;

  return Inertia::render('Board', [
    'gameId'   => $game->id,
    'player'   => $gameState['player'],
    'hand'     => $gameState['player']->hand->cards,
    'opponent' => $opponent,
    'turn'   => $gameState['player']->isTurn ? 'Yours' : $opponent . '\'s',
    'isTurn' => $gameState['player']->isTurn,
    'stock' => $stock,
    'discard' => $discard,
    // turn is random at the start of the game
  ]);
})->middleware(['auth', 'verified'])->name('game.play');

require __DIR__ . '/auth.php';
