<?php

namespace App\Http\Controllers;

use App\Events\PlayerJoined;
use App\Models\Game;
use Illuminate\Http\Request;

class JoinGameController extends Controller {

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string                    $code
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function __invoke(Request $request, string $code) {
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
    return redirect('/') - with('error', 'Game not found');
  }

}
