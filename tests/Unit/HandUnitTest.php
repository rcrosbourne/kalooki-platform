<?php

use App\Enums\Rank;
use App\Enums\Suit;
use App\Models\Card;
use App\Models\Hand;

it('contains a subset  of cards', function () {
  $hand = new Hand([
    new Card(Suit::clubs, Rank::ace),
    new Card(Suit::clubs, Rank::king),
    new Card(Suit::clubs, Rank::queen),
    new Card(Suit::clubs, Rank::jack),
    new Card(Suit::clubs, Rank::ten),
  ]);
  expect($hand)->toBeInstanceOf(Hand::class);
});

it('can sort cards by suit', function ($hand, $expectation) {
  $hand = new Hand($hand);
  $hand->cards = Hand::sortBySuit($hand->cards);
  expect($hand->cards)->toEqual($expectation);
})->with([
  '2 clubs only' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::king),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::clubs, Rank::ten),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::diamonds, Rank::king),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::spades, Rank::queen),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::diamonds, Rank::king),
    ],
    [
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::spades, Rank::queen),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::diamonds, Rank::king),
    ],
  ],
]);

it('can sort cards by rank', function ($hand, $expectation) {
  $hand = new Hand($hand);
  $hand->cards = Hand::sortByRank($hand->cards);
  expect($hand->cards)->toEqual($expectation);
})->with([
  '10 to Ace' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::king),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::clubs, Rank::ten),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::diamonds, Rank::king),
      new Card(Suit::clubs, Rank::ace),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::diamonds, Rank::king),
    ],
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::diamonds, Rank::king),
      new Card(Suit::clubs, Rank::ace),
    ],
  ],
]);

it('can sort cards by suit then by rank', function ($hand, $expectation) {
  $hand = new Hand($hand);
  $hand->cards = Hand::sortBySuitThenRank($hand->cards);
  expect($hand->cards)->toEqual($expectation);
})->with([
  '10 to Ace' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::king),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::clubs, Rank::ten),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::hearts, Rank::queen),
      new Card(Suit::diamonds, Rank::king),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::diamonds, Rank::king),
    ],
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::ten),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::king),
    ],
  ],
]);

it('can detect a three', function ($hand, $expectation) {
  $hand = new Hand($hand);
  expect($hand->containsThree())->toEqual($expectation);
})->with([
  '1 three with 3 cards' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::ace),
      new Card(Suit::hearts, Rank::ace),
      new Card(Suit::spades, Rank::jack),
      new Card(Suit::clubs, Rank::ten),
    ],
    //After sort
    [
      new Card(Suit::hearts, Rank::ace),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::ace),
    ],
  ],
  '2 three 3 cards each' => [
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::two),
      new Card(Suit::clubs, Rank::four),
      new Card(Suit::diamonds, Rank::two),
      new Card(Suit::diamonds, Rank::four),
    ],
    [
      new Card(Suit::spades, Rank::two),
      new Card(Suit::clubs, Rank::two),
      new Card(Suit::diamonds, Rank::two),
      new Card(Suit::spades, Rank::four),
      new Card(Suit::clubs, Rank::four),
      new Card(Suit::diamonds, Rank::four),
    ],
  ],
  '1 three with 4 cards' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::ace),
      new Card(Suit::hearts, Rank::ace),
      new Card(Suit::spades, Rank::ace),
      new Card(Suit::clubs, Rank::ten),
    ],
    //After sort
    [
      new Card(Suit::spades, Rank::ace),
      new Card(Suit::hearts, Rank::ace),
      new Card(Suit::clubs, Rank::ace),
      new Card(Suit::diamonds, Rank::ace),
    ],
  ],
]);