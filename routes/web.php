<?php

use App\Enums\GameStatus;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Facades\GameCache;
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

Route::post('/kalooki/create', function () {
  $game = new Game();
  $game->code =  Game::generateCode();
  $game->status = GameStatus::created;
  $game->created_by = auth()->user()->id;
  $game->players = [['id' => auth()->user()->id, 'name' => auth()->user()->name]];
  $game->invite_link = route('kalooki.join', ['code' => $game->code]);
  $game->save();
  return redirect('/kalooki/' . $game->id);
})->middleware(['auth', 'verified'])->name('game.create');

Route::get('/kalooki/{game}', function (Game $game) {

  return Inertia::render('WaitingRoom', [
    'gameId' => $game->id,
    'code' => $game->code,
    'players' => $game->players,
    'inviteLink' => $game->invite_link,
    'isCreator' => $game->created_by === auth()->user()->id,
  ]);
});

Route::get('/kalooki/join/{code}', function ($code) {
  $game = Game::where('code', $code)->first();
  if ($game) {
    // validate that the game is not full
    abort_if(count($game->players) >= 2, 404, 'Game is full');
    // validate that the user is not already in the game
    abort_if(in_array(auth()->user()->id, array_column($game->players, 'id')), 404, 'You are already in this game');
    // add player to game
    $game->players = array_merge([['id' => auth()->user()->id, 'name' => auth()->user()->name]], $game->players);
    $game->save();
    broadcast(new PlayerJoined($game->id, ['id' => auth()->user()->id, 'name' => auth()->user()->name]));
    return redirect('/kalooki/' . $game->id);
  }
  return redirect('/')-with('error', 'Game not found');
})->name('kalooki.join')->middleware(['auth', 'verified']);

Route::post('/kalooki/{game}/start', function (Game $game) {
  if(count($game->players) < 2) {
    return redirect('/kalooki/' . $game->id)->with('error', 'You need at least 2 players to start the game');
  }
  abort_if($game->created_by !== auth()->user()->id, 403, 'You are not the creator of this game');
  $game->status = GameStatus::started;
  // set up Kalooki game
  $kalooki = new Kalooki(players: [new Player(name: $game->players[0]['name'], id: $game->players[0]['id']), new Player(name: $game->players[1]['name'], id: $game->players[1]['id'])]);
  $kalooki->deal();
  $kalooki->started = TRUE;
  GameCache::cacheGame($kalooki);
  $game->save();
  broadcast(new GameStarted($game->id, $game->players[0]['id'] ));
  broadcast(new GameStarted($game->id, $game->players[1]['id'] ));
})->middleware(['auth', 'verified'])->name('game.start');

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
