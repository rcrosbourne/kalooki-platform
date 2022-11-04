<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WaitingRoomController extends Controller {

  /**
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Game          $game
   *
   * @return \Inertia\Response
   */
  public function __invoke(Request $request, Game $game) {
    return Inertia::render('WaitingRoom', [
      'gameId'     => $game->id,
      'code'       => $game->code,
      'players'    => $game->players,
      'inviteLink' => $game->invite_link,
      'isCreator'  => $game->created_by === auth()->user()->id,
    ]);
  }

}
