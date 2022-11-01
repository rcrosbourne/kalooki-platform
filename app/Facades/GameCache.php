<?php
namespace App\Facades;

use App\Services\GameCacheService;
use Illuminate\Support\Facades\Facade;

/**
 * @method cacheGame(\App\Models\Kalooki $game)
 * @method getGameState(string $id)
 */
class GameCache extends Facade {

  public static function getFacadeAccessor(): string {
    return GameCacheService::class;
  }

}