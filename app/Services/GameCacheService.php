<?php
namespace App\Services;
use App\Models\Kalooki;
use Illuminate\Support\Facades\Cache;

class GameCacheService {

  /**
   * Caches current game state.
   *
   * @param  \App\Models\Kalooki  $game
   *
   * @return void
   */
  public function cacheGame(Kalooki $game): void {
    Cache::put($game->id(), $game);
    foreach ($game->players as $player) {
      Cache::put($player->id, ['player' => $player, 'game' => $game]);
    }
  }

  /**
   * Returns the current game state
   *
   * @param  string  $id The game id or Player id
   *
   * @return array|null
   */
  public function getGameState(string $id): ?array {
    $player = NULL;
    $game = NULL;

    $cacheValue = Cache::get($id);
    if(!$cacheValue) {
      return NULL;
    }
    if(isset($cacheValue['player'])) {
      $player = $cacheValue['player'];
      $game = $cacheValue['game'];
    }
    return [
      'game' => $game,
      'player' => $player,
    ];
  }
}