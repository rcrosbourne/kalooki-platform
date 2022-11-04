<?php

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Models\Game;
use Illuminate\Http\Request;

class CreateGameController extends Controller {

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function __invoke(Request $request) {
    $game = new Game();
    $game->code = Game::generateCode();
    $game->status = GameStatus::created;
    $game->created_by = auth()->user()->id;
    $game->players = [['id' => auth()->user()->id, 'name' => auth()->user()->name]];
    $game->invite_link = route('kalooki.join', ['code' => $game->code]);
    $game->save();
    return redirect('/kalooki/' . $game->id);
  }

}
