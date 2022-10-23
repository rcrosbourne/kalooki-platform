<?php

use App\Exceptions\NotEnoughCardsException;
use App\Models\Deck;

it("has a standard 52 card deck", function () {
  Deck::initialize();
  expect(count(Deck::cards()))->toBe(52);
});

it('can shuffle cards in a random order', function () {
  Deck::initialize();
  $cards = Deck::cards();
  Deck::shuffle();
  expect(Deck::cards())->not->toBe($cards);
});

it('can deal a select number of cards', function () {
  Deck::initializeAndShuffle();
  $dealtCards = Deck::deal(5);
  expect(count($dealtCards))->toBe(5)
    ->and(count(Deck::cards()))->toBe(47);
});
it('cannot deal more cards than what is available', function () {
  Deck::initializeAndShuffle();
  Deck::deal(52);
  expect(fn () => Deck::deal(1))->toThrow(NotEnoughCardsException::class);
});