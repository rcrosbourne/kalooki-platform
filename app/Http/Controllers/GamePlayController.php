<?php

namespace App\Http\Controllers;

use App\Facades\GameCache;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GamePlayController extends Controller
{
    public function __invoke(Request $request, Game $game)
    {
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
        'availableActions' => $gameState['player']->isTurn ? $gameState['player']->availableActions : [],
        // turn is random at the start of the game
      ]);

    }
}
