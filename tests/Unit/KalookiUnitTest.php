<?php

use App\Exceptions\IllegalActionException;
use App\Models\Card;
use App\Models\Kalooki;
use App\Models\Player;
use Tests\TestCase;

uses(TestCase::class);
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

it('has an id for game and players when created', function () {
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
 $player = $game->players[0];
     expect($game->id())->toBeString()->and($player->id)->toBeString();
});

it('caches game and player state when game is created', function () {
  $game = Kalooki::fake([
        'players' => [
          Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
          Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
        ],
        'discard' => ['7♠'],
        'stock' => [
          '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
          '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
        ],
      ]);
  $player1 = $game->players[0];
  $player2 = $game->players[1];
  $gameId = $game->id();
  $player1Id = $game->players[0]->id;
  $player2Id = $game->players[1]->id;
  $gameFromCache =  \Illuminate\Support\Facades\Cache::get($gameId);
  $player1FromCache = \Illuminate\Support\Facades\Cache::get($player1Id)['player'];
  $player2FromCache = \Illuminate\Support\Facades\Cache::get($player2Id)['player'];
  expect($gameFromCache)->toBe($game)
    ->and($player1FromCache)->toBe($player1)
    ->and($player2FromCache)->toBe($player2);
});

it('allows a player to draw from the stock pile of cards', function () {
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
    expect($player1->hand->cards)->toHaveCount(12)
      ->and($game->stock)->toHaveCount(27);
    $player1->drawFromStockPile();
    expect($player1->hand->cards)->toHaveCount(13)
      ->and($game->stock)->toHaveCount(26);
});
it('allows a player to discard a card from hand', function () {

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
  /** @var \App\Models\Player $player1 */
  $player1 = $game->players[0];
  /** @var \App\Models\Card $card */
  $card  = $player1->hand->cards[0]; // A♠
  $player1->drawFromStockPile();
  expect($player1->hand->cards)->toHaveCount(13)
    ->and($game->discard)->toHaveCount(1);
  $player1->discardFromHand($card);
  expect($player1->hand->cards)->toHaveCount(12)
    ->and($game->discard)->toHaveCount(2);
});

it('does not allows a player to discard a card not currently in hand', function () {

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
  $card  = $game->stock[0]; // 7♥
  expect($player1->hand->cards)->toHaveCount(12)
    ->and($game->discard)->toHaveCount(1)
    ->and(function () use ($player1, $card) {
      $player1->discardFromHand($card);
    })->toThrow(IllegalActionException::class);
});

it('allows a player to draw a card from the discard pile', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    expect($player1->hand->cards)->toHaveCount(12)
      ->and($game->discard)->toHaveCount(2);
    $player1->drawFromDiscardPile();
    expect($player1->hand->cards)->toHaveCount(13)
      ->and($game->discard)->toHaveCount(1);
});

it('does not allow a player to draw a card from discard pile if the pile is empty', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => [],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    expect($player1->hand->cards)->toHaveCount(12)
      ->and($game->discard)->toHaveCount(0)
      ->and(function () use ($player1) {
        $player1->drawFromDiscardPile();
      })->toThrow(IllegalActionException::class)
      ->and($player1->hand->cards)->toHaveCount(12)
      ->and($game->discard)->toHaveCount(0);
});
it('adds card to the top of the discard pile and removes it when player draws from it', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    $card1 = $game->discard[0];
    $card2 = $game->discard[1];
    $player1->drawFromDiscardPile();
    expect($player1->hand->cards)->toHaveCount(13)
      ->and($game->discard)->toHaveCount(1)
      ->and($game->discard[0])->toBe($card1)
    ->and($player1->hand->cards)->toContain($card2);
});

it('allows a player to lay down cards', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    /** @var Player $player1 */
    $player1 = $game->players[0];
    $player1->drawFromStockPile();
    $player1->layDownCards();
    expect($player1->hand->cards)->toHaveCount(3)
      ->and($player1->laidDownThrees)->toHaveCount(6)
      ->and($player1->laidDownFours)->toHaveCount(4);
});

it('it does not allow a player to lay down cards if the contract is not satisfied', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'K♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
    $player1 = $game->players[0];
    expect(function () use ($player1) {
        $player1->layDownCards();
    })->toThrow(IllegalActionException::class)
      ->and($player1->hand->cards)->toHaveCount(12)
      ->and($player1->laidDownThrees)->toHaveCount(0)
      ->and($player1->laidDownFours)->toHaveCount(0);
});

it('notifies a player when it is their turn to play', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
    ],
    'discard' => ['7♠', '7♥'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  expect($player1->isTurn)->toBeFalse();
  $game->setTurn($player1->id);
  expect($player1->isTurn)->toBeTrue();
});

it('only one player can have their turn at any given time', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
    ],
    'discard' => ['7♠', '7♥'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  $player2 = $game->players[1];
  expect($player1->isTurn)->toBeFalse()
    ->and($player2->isTurn)->toBeFalse();
  $game->setTurn($player1->id);
  expect($player1->isTurn)->toBeTrue()->and($player2->isTurn)->toBeFalse();
  $game->setTurn($player2->id);
  expect($player1->isTurn)->toBeFalse()->and($player2->isTurn)->toBeTrue();
});

it('determines a player\'s available actions in a given turn if contract satisfied', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
    ],
    'discard' => ['7♠', '7♥'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  $player1 = $game->players[0];
  $game->setTurn($player1->id);
  expect($player1->availableActions())->toHaveCount(2)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::discardCardFromHand)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::layDownCards)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
});

it('determines a player\'s available actions in a given turn if contract not satisfied', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', '10♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
 $player1 = $game->players[0];
 $game->setTurn($player1->id);
 $player1->drawFromStockPile();
  expect($player1->availableActions())->toHaveCount(1)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::discardCardFromHand)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::layDownCards)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
});

it('determines a player\'s available actions in a given turn if discard pile is empty', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', '10♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => [],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
 $player1 = $game->players[0];
 $game->setTurn($player1->id);
  expect($player1->availableActions())->toHaveCount(1)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::discardCardFromHand)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::layDownCards)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
});

it('reevaluates a player\'s available actions in a given turn based on the actions they have already taken', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '4♣', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
 /** @var Player $player1 */
 $player1 = $game->players[0];
 $game->setTurn($player1->id);
  expect($player1->availableActions())->toHaveCount(2)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
  $player1->drawFromStockPile();
  expect($player1->availableActions())->toHaveCount(2)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::discardCardFromHand)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::layDownCards);
  $player1->layDownCards();
  expect($player1->availableActions())->toHaveCount(1)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::discardCardFromHand);
  $player1->discardFromHand($player1->hand->cards[0]);
  expect($player1->availableActions())->toHaveCount(1)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::endTurn);
});

it('reevaluates a player\'s available actions in a given turn if the player does a winning action', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', '8♣', '6♣']]),
      ],
      'discard' => ['7♠', '7♥', '7♣'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
 /** @var Player $player1 */
 $player1 = $game->players[0];
 $game->setTurn($player1->id);
  expect($player1->availableActions())->toHaveCount(2)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
  $player1->drawFromDiscardPile();
  expect($player1->availableActions())->toHaveCount(2)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::discardCardFromHand)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::layDownCards);
  $player1->layDownCards();
  // Winner
  expect($player1->availableActions())->toHaveCount(0)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::discardCardFromHand);
});

it('reevaluates a player\'s available actions in a given turn if the laid out cards', function () {
  $game = Kalooki::fake([
    'players' => [
      Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', '8♣', '6♣']]),
    ],
    'discard' => ['7♠', '7♥', '7♣'],
    'stock' => [
      '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
      '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
    ],
  ]);
  /** @var Player $player1 */
  $player1 = $game->players[0];
  // simulating laying down cards on the previous turn
  $game->setTurn($player1->id);
  $player1->drawFromStockPile();
  $player1->layDownCards();
  $player1->discardFromHand($player1->hand->cards[1]);
  $player1->endTurn();
  // Next turn player can only request card from stockpile
  $game->setTurn($player1->id);
  expect($player1->availableActions())->toHaveCount(1)
    ->and($player1->availableActions())->not()->toContain(\App\Enums\PlayerActions::requestCardFromDiscardPile)
    ->and($player1->availableActions())->toContain(\App\Enums\PlayerActions::requestCardFromStockPile);
});

it('throws an exception if a player does an action that is not available', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', 'A♣', '10♣']]),
      ],
      'discard' => ['7♠', '7♥', '7♣'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
 /** @var Player $player1 */
 $player1 = $game->players[0];
 $game->setTurn($player1->id);
  expect(function () use ($player1) {
    $player1->layDownCards();
  })->toThrow(\App\Exceptions\IllegalActionException::class)
    ->and(function () use ($player1) {
      $player1->endTurn();
    })->toThrow(\App\Exceptions\IllegalActionException::class)
    ->and(function () use ($player1) {
      $player1->discardFromHand($player1->hand->cards[0]);
    })->toThrow(\App\Exceptions\IllegalActionException::class);
  // Draw from card
  $player1->drawFromDiscardPile();
  expect(function () use ($player1) {
    $player1->layDownCards();
  })->toThrow(\App\Exceptions\IllegalActionException::class)
  ->and(function () use ($player1) {
    $player1->endTurn();
  })->toThrow(\App\Exceptions\IllegalActionException::class);

});

it('detects the next player\'s turn', function () {
 $game = Kalooki::fake([
      'players' => [
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', 'A♣', '10♣']]),
        Player::fake(['hand' => ['A♠', 'A♥', 'A♦', '2♠', '2♥', '2♦', '3♣', '4♣', '5♣', 'A♣', '10♣']]),
      ],
      'discard' => ['7♠', '7♥', '7♣'],
      'stock' => [
        '7♥', '7♦', '7♣', '8♠', '8♥', '8♦', '8♣', '9♠', '9♥', '9♦', '9♣', '10♠', '10♥',
        '10♦', '10♣', 'J♠', 'J♥', 'J♦', 'J♣', 'Q♠', 'Q♥', 'Q♦', 'Q♣', 'K♠', 'K♥', 'K♦', 'K♣'
      ],
    ]);
  /** @var Player $player1 */
  $player1 = $game->players[0];
  /** @var Player $player2 */
  $player2 = $game->players[1];
  $game->setTurn($player1->id);
  expect($player1->isTurn)->toBeTrue()
    ->and($player2->isTurn)->toBeFalse();

  $player1->drawFromStockPile();
  $player1->discardFromHand($player1->hand->cards[0]);
  $player1->endTurn();
  expect($player1->isTurn)->toBeFalse()
    ->and($player2->isTurn)->toBeTrue();

});