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
      /** @var \App\Models\Player $player */
      $player = $gameState['player'];
      /** @var \App\Models\Player $opponent */
      $opponent = $players[array_search(auth()->user()->name, array_column($players, 'name')) === 0 ? 1 : 0];
      $stock  = $kalooki->stock;
      $discard = $kalooki->discard;

      return Inertia::render('Board', [
        'gameId'   => $game->id,
        'player'   => $player,
        'hand'     => $player->hand->cards,
        'opponent' => $opponent->name,
        'turn'   => $player->isTurn ? 'Yours' : $opponent->name . '\'s',
        'isTurn' => $player->isTurn,
        'stock' => $stock,
        'discard' => $discard,
        'availableActions' => $player->isTurn ? $player->availableActions : [],
        'playerTopThrees' => $player->topThrees,
        'playerBottomThrees' => $player->bottomThrees,
        'playerFours' => $player->laidDownFours,
        'opponentTopThrees' => $opponent->topThrees,
        'opponentBottomThrees' => $opponent->bottomThrees,
        'opponentFours' => $opponent->laidDownFours,
        // turn is random at the start of the game
      ]);

    }
}
