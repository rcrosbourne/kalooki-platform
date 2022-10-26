<?php

use App\Models\Card;
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
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->contractSatisfied())->toHaveCount(2);
});
it('can detect when a player does not have a solution', function () {
 $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', '2♠', '2♥', '2♦', '2♣', '9♣', '10♣', 'Q♣', 'A♣', 'K♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->contractSatisfied())->toHaveCount(0);
});

it('can detect when a player has overlapping cards but have a solution', function () {
 $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['J♠', 'J♥', 'J♦', '2♠', '2♥', '2♦', '2♣', '10♣', 'J♣', 'Q♣', 'A♣', 'K♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->contractSatisfied())->toHaveCount(2);
});

it('can detect when a player has long sequence but have a solution', function () {
 $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['10♠', '10♥', 'J♦', '2♠', '2♥', '2♦', '2♣', '9♣', '10♣', 'J♣', 'Q♣', 'A♣', 'K♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->contractSatisfied())->toHaveCount(2);
});

it('can detect when a player has 2 sequences but only one is a valid solution', function () {
 $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['J♠', 'J♥', 'J♦', 'K♠', 'K♥','2♠', '3♠', '4♠', '5♠', 'J♣', 'Q♣', 'A♣', 'K♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->contractSatisfied())->toHaveCount(2);
});
it('it validates that the winning conditions do not have cards in common', function () {
 $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '2♣', '3♣', '4♣', '5♣', 'A♣', 'K♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  $solution = $player1->contractSatisfied();
  $cardsUsedInThrees = collect($solution['threes'])->flatten()->toArray();
  $cardsUsedInFours = collect($solution['fours'])->flatten()->toArray();
  // validate that cards used in threes and fours are not in common
  expect(array_intersect($cardsUsedInThrees, $cardsUsedInFours))->toHaveCount(0);
});

it('adds eligible cards to the end of four if applicable', function () {
  $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '2♣', '3♣', '4♣', '5♣', 'A♣', '6♣']]),
      ],
      'discard' => ['7♠'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    $solution = $player1->contractSatisfied();
    $cardsUsedInSolution = collect($solution)->flatten()->toArray();
    // validate that cards used in threes and fours are not in common
    // no cards should be left over
    expect(array_diff($player1->hand->cards, $cardsUsedInSolution))->toHaveCount(0);
});

it('adds eligible cards if applicable', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '7♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
    ],
    'discard' => ['7♠'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  $solution = $player1->contractSatisfied();
  $cardsUsedInSolution = collect($solution)->flatten()->toArray();
  // validate that cards used in threes and fours are not in common
  // no cards should be left over
  expect(array_diff($player1->hand->cards, $cardsUsedInSolution))->toHaveCount(0);
});

it('does not add ineligible cards if applicable', function () {
  $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    $solution = $player1->contractSatisfied();
    $cardsUsedInSolution = collect($solution)->flatten()->toArray();
    // validate that cards used in threes and fours are not in common
    //'8♣' and '4♣' should not be used
    // validate that '8♣' and '4♣' are not in the solution
    $unusedCards = array_diff($player1->hand->cards, $cardsUsedInSolution);
    $eightClubs = array_filter($unusedCards, function (Card $card) {
      return $card->suit === \App\Enums\Suit::clubs && $card->rank === \App\Enums\Rank::eight;
    });
    $fourClubs = array_filter($unusedCards, function (Card $card) {
      return $card->suit === \App\Enums\Suit::clubs && $card->rank === \App\Enums\Rank::four;
    });
    expect($eightClubs)->toHaveCount(1)
      ->and($fourClubs)->toHaveCount(1)
      ->and($unusedCards)->toHaveCount(2);
});