<?php

use App\Enums\Rank;
use App\Enums\Suit;
use App\Models\Card;

it('can create card from string', function () {
  $card = Card::fromString('A♥');
  expect($card->rank)->toBe(Rank::ace)
    ->and($card->suit)->toBe(Suit::hearts);
});
it('throws an exception when string is invalid', function () {
  expect(fn() => Card::fromString('♠A'))->toThrow(\Exception::class)
    ->and(fn() => Card::fromString('As'))->toThrow(\Exception::class);
});