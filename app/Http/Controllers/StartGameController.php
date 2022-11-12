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
   * @throws \Exception
   */
  public function __invoke(Request $request, Game $game) {

    if (count($game->players) < 2) {
      return redirect('/kalooki/' . $game->id)->with('error', 'You need at least 2 players to start the game');
    }
    abort_if($game->created_by !== auth()->user()->id, 403, 'You are not the creator of this game');
    $game->status = GameStatus::started;
    // set up Kalooki game
//    $kalooki = Kalooki::fake([
//      'id' => $game->id,
//      'players' => [
//        Player::fake(['id' => $game->players[0]['id'], 'hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
//        Player::fake(['id' => $game->players[1]['id'], 'hand' => ['A♠', 'A♥', 'K♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
//      ],
//      'discard' => ['7♠', '7♥'],
//      'stock' => [
//        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
//        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
//      ],
//    ]);
//    $kalooki = Kalooki::fake([
//      'id' => $game->id,
//    'players' => [
//      Player::fake(['id' => $game->players[0]['id'], 'name' => "Player 1",'hand' => ['K♠', 'K♥', 'K♦', '3♠', '3♥', '3♦', '10♣', 'J♣', 'Q♣', 'K♣', '6♣']]),
//      Player::fake(['id' => $game->players[1]['id'], 'name' => "Player 2", 'hand' => ['Q♠', 'Q♥', 'Q♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', 'A♣', '6♣']]),
//    ],
//    'discard' => ['7♠', '7♥', '7♣'],
//    'stock' => [
//      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
//      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', '7♣','3♣'
//    ],
//  ]);
    $kalooki = new Kalooki(
      id: $game->id,
      players: [
      new Player(name: $game->players[0]['name'], id: $game->players[0]['id']),
      new Player(name: $game->players[1]['name'], id: $game->players[1]['id']),
    ]);
    $kalooki->deal();
    $kalooki->started = TRUE;
    // Set the players available actions
    $kalooki->players[rand(0, 1)]->isTurn = TRUE;
//    $kalooki->players[0]->isTurn = TRUE;
    $this->setPlayerActions($kalooki);
    GameCache::cacheGame($kalooki);
    $game->save();
    broadcast(new GameStarted($game->id, $game->players[0]['id']));
    broadcast(new GameStarted($game->id, $game->players[1]['id']));
  }

  /**
   * @param  \App\Models\Kalooki  $kalooki
   *
   * @return void
   */
  protected function setPlayerActions(Kalooki $kalooki): void {
    foreach ($kalooki->players as $player) {
      $player->availableActions = $player->isTurn ? $kalooki->getAvailableActions($player, $kalooki) : [];
    }
  }

}
