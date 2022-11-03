<?php

use App\Enums\GameStatus;
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
  $game->save();
  return redirect('/kalooki/' . $game->id);
})->middleware(['auth', 'verified'])->name('game.create');

require __DIR__ . '/auth.php';
