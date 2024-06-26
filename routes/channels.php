<?php

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
  return (int) $user->id === (int) $id;
});
Broadcast::channel('game-{game}', function (User $user, Game $game) {
  return $game->creator->id === $user->id;
});
Broadcast::channel('started.{game}.{recipient}', function (User $user, Game $game, User $recipient) {
  return $user->id === $recipient->id;
});

Broadcast::channel('game.{game}.{player}', function (User $user, Game $game, User $player) {
  return !empty($game) && $user->id === $player->id;
});
