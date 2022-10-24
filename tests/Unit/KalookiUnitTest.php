<?php

use App\Models\Kalooki;
use App\Models\Player;

it('uses 2 standard 52 card decks', function () {
  $kalooki = new Kalooki();
  expect(count($kalooki->deck))->toBe(104);
});

it('is unable to start unless there are at least 2 players', function () {
  $kalooki = new Kalooki();
  $kalooki->addPlayer(new Player('Player 1'));
  $kalooki->start();
  expect($kalooki->isStarted())->toBeFalse();
  $kalooki->addPlayer(new Player('Player 2'));
  $kalooki->start();
  expect($kalooki->isStarted())->toBeTrue();
});

it('can deal 12 cards each to players', function () {
  $game = new Kalooki();
  $game->addPlayer(new Player('Player 1'));
  $game->addPlayer(new Player('Player 2'));
  $game->deal();
  expect($game->players[0]->hand->cards)->toHaveCount(12)
    ->and($game->players[1]->hand->cards)->toHaveCount(12);
});

it('contains a discard pile and stock pile after dealing cards to players', function () {
  $game = new Kalooki();
  $game->addPlayer(new Player('Player 1'));
  $game->addPlayer(new Player('Player 2'));
  $game->deal();
  expect($game->stock)->toHaveCount(79)
    ->and($game->discard)->toHaveCount(1);
});

it('can detect when a player satisfies the contract condition', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '2♣', '3♣', '4♣', '5♣', 'A♣', 'K♣']]),
      Player::fake(['hand' => ['4♠', '4♥', '4♦', '4♣', '5♠', '5♥', '5♦', '5♣', '6♠', '6♥', '6♦', '6♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  $player2 = $game->players[1];
  expect($player1->contractSatisfied())->toBeTrue();
})->skip();
