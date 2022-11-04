<?php

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Events\GameStarted;
use App\Facades\GameCache;
use App\Models\Game;
use App\Models\Kalooki;
use App\Models\Player;
use Illuminate\Http\Request;

class StartGameController extends Controller {

  /**
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Game          $game
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
   */
  public function __invoke(Request $request, Game $game) {

    if (count($game->players) < 2) {
      return redirect('/kalooki/' . $game->id)->with('error', 'You need at least 2 players to start the game');
    }
    abort_if($game->created_by !== auth()->user()->id, 403, 'You are not the creator of this game');
    $game->status = GameStatus::started;
    // set up Kalooki game
    $kalooki = new Kalooki(players: [
      new Player(name: $game->players[0]['name'], id: $game->players[0]['id']),
      new Player(name: $game->players[1]['name'], id: $game->players[1]['id']),
    ]);
    $kalooki->deal();
    $kalooki->started = TRUE;
    GameCache::cacheGame($kalooki);
    $game->save();
    broadcast(new GameStarted($game->id, $game->players[0]['id']));
    broadcast(new GameStarted($game->id, $game->players[1]['id']));
  }

}
