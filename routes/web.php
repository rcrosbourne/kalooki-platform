<?php

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
  return Inertia::render('Board', []);
//      return Inertia::render('Welcome', [
//          'canLogin' => Route::has('login'),
//          'canRegister' => Route::has('register'),
//          'laravelVersion' => Application::VERSION,
//          'phpVersion' => PHP_VERSION,
//      ]);
});
Route::get('/mantine', function () {
  return Inertia::render('MantineBoard', []);
});
Route::get('/kalooki', function () {
  $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
//    expect($player1->hand->cards)->toHaveCount(12);
    $player1 = $game->players[0];
    $player1->drawFromStock($game);

});

Route::get('/dashboard', function () {
  return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';
