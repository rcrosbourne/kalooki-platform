<?php

use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Facades\GameCache;
use App\Http\Controllers\CreateGameController;
use App\Http\Controllers\GamePlayController;
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

Route::get('/kalooki/{game}/play', GamePlayController::class)->middleware(['auth', 'verified'])->name('game.play');
Route::get('/kalooki/{game}/available-moves', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    // return available actions
    return response()->json($player->availableActions());
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.available-moves');
Route::post('/kalooki/{game}/request-card-from-stock-pile', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    // request card from stock pile
    $player->drawFromStockPile();
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Player $player */
    $player = $gameState['player'];
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'hand'             => $player->hand->cards,
      'stock'            => $gameState['game']->stock,
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.request-card-from-stock-pile');

Route::post('/kalooki/{game}/request-card-from-discard-pile', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    // request card from stock pile
    $player->drawFromDiscardPile();
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Player $player */
    $player = $gameState['player'];
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'hand'             => $player->hand->cards,
      'discard'          => $gameState['game']->discard,
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.request-card-from-discard-pile');

Route::post('/kalooki/{game}/lay-cards', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    // request card from stock pile
    $player->layDownCards();
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Player $player */
    $player = $gameState['player'];
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'hand'             => $player->hand->cards,
      'topThrees'        => $player->topThrees,
      'bottomThrees'     => $player->bottomThrees,
      'fours'            => $player->laidDownFours,
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.lay-down-cards');

Route::post('/kalooki/{game}/tack-on-cards', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    // Tack on cards
    $player->tackOnCards();
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Player $player */
    $player = $gameState['player'];
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'hand'             => $player->hand->cards,
      'topThrees'        => $player->topThrees,
      'bottomThrees'     => $player->bottomThrees,
      'fours'            => $player->laidDownFours,
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.tack-on-cards');

Route::post('/kalooki/{game}/discard-card-from-hand', function (Game $game) {
  // load game from cache
  // Card
  $discardCardId = request()->input('card');
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    //discard card from hand
    $cardToBeDiscarded = collect($player->hand->cards)->firstWhere('id', $discardCardId);
    $player->discardFromHand($cardToBeDiscarded);
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Player $player */
    $player = $gameState['player'];
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'hand'             => $player->hand->cards,
      'discard'          => $gameState['game']->discard,
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.discard-card-from-hand');

Route::post('/kalooki/{game}/reorder-hand', function (Game $game) {
  // load game from cache
  $gameState = GameCache::getGameState(auth()->user()->id);
  $fromIndex = request()->input('from');
  $toIndex = request()->input('to');
  /** @var Player $player */
  $player = $gameState['player'];
  // reorder hand
  $player->reorderHand($fromIndex, $toIndex);
  // reload game state
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // return available actions
  return response()->json([
    'availableActions' => $player->availableActions(),
    'hand'             => $player->hand->cards,
  ]);
})->middleware(["auth", "verified"])->name('game.reorder-hand');


Route::post('/kalooki/{game}/end-turn', function (Game $game) {
  $gameState = GameCache::getGameState(auth()->user()->id);
  /** @var Player $player */
  $player = $gameState['player'];
  // validate that it's your turn
  if ($player->isTurn) {
    //discard card from hand
    $player->endTurn();
    // reload game state
    $gameState = GameCache::getGameState(auth()->user()->id);
    /** @var Kalooki $game */
    $game = $gameState['game'];
    /** @var Player $player */
    $player = $gameState['player'];
    if($player->isWinner) {
      return response()->json([
        'availableActions' => $player->availableActions(),
        'hand'             => $player->hand->cards,
        'topThrees'        => $player->topThrees,
        'bottomThrees'     => $player->bottomThrees,
        'fours'            => $player->laidDownFours,
        'winner'           => $player->name,
      ]);
    }
    $turn = array_values(
              array_filter($game->players, function ($player) {
                return $player->isTurn;
              })
            )[0]->name;
    // return available actions
    return response()->json([
      'availableActions' => $player->availableActions(),
      'isTurn'           => $player->isTurn,
      'hand'             => $player->hand->cards,
      'turn'             => "{$turn}'s",
    ]);
  }
  return response()->json([]);
})->middleware(["auth", "verified"])->name('game.end-turn');


require __DIR__ . '/auth.php';
