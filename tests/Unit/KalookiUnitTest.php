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
  $kalooki = new Kalooki();
  $kalooki->addPlayer(new Player('Player 1'));
  $kalooki->addPlayer(new Player('Player 2'));
  $kalooki->deal();
  expect($kalooki->players[0]->hand)->toHaveCount(12)
    ->and($kalooki->players[1]->hand)->toHaveCount(12)
    ->and($kalooki->deck)->toHaveCount(80);
});
