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
  '2 clubs only'         => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::king, '2'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::diamonds, Rank::king, '2'),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::jack, '1'),
      new Card(Suit::spades, Rank::queen, '2'),
      new Card(Suit::clubs, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::diamonds, Rank::king, '5'),
    ],
    [
      new Card(Suit::spades, Rank::jack, '1'),
      new Card(Suit::spades, Rank::queen, '2'),
      new Card(Suit::clubs, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::diamonds, Rank::king, '5'),
    ],
  ],
]);

it('can sort cards by rank', function ($hand, $expectation) {
  $hand = new Hand($hand);
  $hand->cards = Hand::sortByRank($hand->cards);
  expect($hand->cards)->toEqual($expectation);
})->with([
  '10 to Ace'            => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::king, '2'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::diamonds, Rank::king, '2'),
      new Card(Suit::clubs, Rank::ace, '1'),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::diamonds, Rank::king, '5'),
    ],
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::diamonds, Rank::king, '5'),
      new Card(Suit::clubs, Rank::ace, '3'),
    ],
  ],
]);

it('can sort cards by suit then by rank', function ($hand, $expectation) {
  $hand = new Hand($hand);
  $hand->cards = Hand::sortBySuitThenRank($hand->cards);
  expect($hand->cards)->toEqual($expectation);
})->with([
  '10 to Ace'            => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::king, '2'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::hearts, Rank::queen, '3'),
      new Card(Suit::diamonds, Rank::king, '2'),
    ],
  ],
  '2 spades and 2 clubs' => [
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::diamonds, Rank::king, '5'),
    ],
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::ten, '4'),
      new Card(Suit::clubs, Rank::ace, '3'),
      new Card(Suit::diamonds, Rank::king, '5'),
    ],
  ],
]);

it('returns threes if they exist or empty list if none exist', function ($hand, $expectation) {
  $hand = new Hand($hand);
  expect($hand->containsThree())->toEqual($expectation);
})->with([
  '1 three with 3 cards'  => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::spades, Rank::jack, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
    ],
  ],
  '2 three 3 cards each'  => [
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::two, '3'),
      new Card(Suit::clubs, Rank::four, '4'),
      new Card(Suit::diamonds, Rank::two, '5'),
      new Card(Suit::diamonds, Rank::four, '6'),
    ],
    [
      new Card(Suit::spades, Rank::two, '1'),
      new Card(Suit::clubs, Rank::two, '3'),
      new Card(Suit::diamonds, Rank::two, '5'),
      new Card(Suit::spades, Rank::four, '2'),
      new Card(Suit::clubs, Rank::four, '4'),
      new Card(Suit::diamonds, Rank::four, '6'),
    ],
  ],
  '1 three with 4 cards'  => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::spades, Rank::ace, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::spades, Rank::ace, '4'),
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
    ],
  ],
  '2 threes with 4 cards' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::spades, Rank::ace, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::hearts, Rank::ten, '6'),
      new Card(Suit::diamonds, Rank::ten, '7'),
      new Card(Suit::spades, Rank::ten, '8'),
    ],
    //After sort
    [
      new Card(Suit::spades, Rank::ten, '8'),
      new Card(Suit::hearts, Rank::ten, '6'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::diamonds, Rank::ten, '7'),
      new Card(Suit::spades, Rank::ace, '4'),
      new Card(Suit::hearts, Rank::ace, '3'),
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
    ],
  ],
  '0 threes'              => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::ace, '1'),
      new Card(Suit::diamonds, Rank::ace, '2'),
      new Card(Suit::hearts, Rank::three, '3'),
      new Card(Suit::spades, Rank::four, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::hearts, Rank::king, '6'),
      new Card(Suit::diamonds, Rank::jack, '7'),
      new Card(Suit::spades, Rank::queen, '8'),
    ],
    //After sort
    [],
  ],
]);

it('returns fours if they exist or empty list if none exist', function ($hand, $expectation) {
  $hand = new Hand($hand);
  expect($hand->containsFour())->toEqual($expectation);
})->with([
  '1 four with 4 cards'                                      => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::four, '1'),
      new Card(Suit::clubs, Rank::two, '2'),
      new Card(Suit::clubs, Rank::three, '3'),
      new Card(Suit::clubs, Rank::five, '4'),
      new Card(Suit::hearts, Rank::ten, '5'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::two, '2'),
      new Card(Suit::clubs, Rank::three, '3'),
      new Card(Suit::clubs, Rank::four, '1'),
      new Card(Suit::clubs, Rank::five, '4'),
    ],
  ],
  '1 four with 4 cards with 2 cards out of sequence (start)' => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::three, '1'),
      new Card(Suit::clubs, Rank::five, '2'),
      new Card(Suit::clubs, Rank::six, '3'),
      new Card(Suit::clubs, Rank::seven, '4'),
      new Card(Suit::clubs, Rank::eight, '5'),
      new Card(Suit::clubs, Rank::ten, '6'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::five, '2'),
      new Card(Suit::clubs, Rank::six, '3'),
      new Card(Suit::clubs, Rank::seven, '4'),
      new Card(Suit::clubs, Rank::eight, '5'),
    ],
  ],
  '1 four with 4 cards with 2 cards out of sequence(end)'    => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::five, '1'),
      new Card(Suit::clubs, Rank::six, '2'),
      new Card(Suit::clubs, Rank::seven, '3'),
      new Card(Suit::clubs, Rank::eight, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::clubs, Rank::ace, '6'),
    ],
    //After sort
    [
      new Card(Suit::clubs, Rank::five, '1'),
      new Card(Suit::clubs, Rank::six, '2'),
      new Card(Suit::clubs, Rank::seven, '3'),
      new Card(Suit::clubs, Rank::eight, '4'),
    ],
  ],
  '2 fours with 4 cards'                                     => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::five, '1'),
      new Card(Suit::clubs, Rank::six, '2'),
      new Card(Suit::clubs, Rank::seven, '3'),
      new Card(Suit::clubs, Rank::eight, '4'),
      new Card(Suit::hearts, Rank::ten, '5'),
      new Card(Suit::hearts, Rank::jack, '6'),
      new Card(Suit::hearts, Rank::queen, '7'),
      new Card(Suit::hearts, Rank::king, '8'),
    ],
    //After sort
    [
      new Card(Suit::hearts, Rank::ten, '5'),
      new Card(Suit::hearts, Rank::jack, '6'),
      new Card(Suit::hearts, Rank::queen, '7'),
      new Card(Suit::hearts, Rank::king, '8'),
      new Card(Suit::clubs, Rank::five, '1'),
      new Card(Suit::clubs, Rank::six, '2'),
      new Card(Suit::clubs, Rank::seven, '3'),
      new Card(Suit::clubs, Rank::eight, '4'),
    ],
  ],
  '2 fours 1 with 4 cards and 1 with 5 cards'                => [
    // Before Sort
    [
      new Card(Suit::diamonds, Rank::three, '1'),
      new Card(Suit::clubs, Rank::ace, '2'),
      new Card(Suit::clubs, Rank::five, '3'),
      new Card(Suit::clubs, Rank::six, '4'),
      new Card(Suit::clubs, Rank::seven, '5'),
      new Card(Suit::clubs, Rank::eight, '6'),
      new Card(Suit::hearts, Rank::ten, '7'),
      new Card(Suit::hearts, Rank::jack, '8'),
      new Card(Suit::hearts, Rank::queen, '9'),
      new Card(Suit::hearts, Rank::king, '10'),
      new Card(Suit::hearts, Rank::ace, '11'),
    ],
    //After sort
    [
      new Card(Suit::hearts, Rank::ten, '7'),
      new Card(Suit::hearts, Rank::jack, '8'),
      new Card(Suit::hearts, Rank::queen, '9'),
      new Card(Suit::hearts, Rank::king, '10'),
      new Card(Suit::hearts, Rank::ace, '11'),
      new Card(Suit::clubs, Rank::five, '3'),
      new Card(Suit::clubs, Rank::six, '4'),
      new Card(Suit::clubs, Rank::seven, '5'),
      new Card(Suit::clubs, Rank::eight, '6'),
    ],
  ],
  '0 fours'                                                  => [
    // Before Sort
    [
      new Card(Suit::clubs, Rank::five, '1'),
      new Card(Suit::hearts, Rank::six, '2'),
      new Card(Suit::diamonds, Rank::seven, '3'),
      new Card(Suit::spades, Rank::eight, '4'),
      new Card(Suit::clubs, Rank::ten, '5'),
      new Card(Suit::clubs, Rank::ace, '6'),
    ],
    //After sort
    [
    ],
  ],
]);