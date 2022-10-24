<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;
use Illuminate\Support\Str;

class Card {
  public function __construct(public Suit $suit, public Rank $rank, public ?string $id = null) {
    $this->id  = $id ?: (string) Str::orderedUuid();
  }

  /**
   * @throws \Exception
   */
  public static function fromString(string $card): Card {
    // string is in the form 'A♠' or '10♦'
    preg_match("/([2-9JQKA]|10)(♠|♥|♣|♦)/", $card, $matches);
    if(count($matches) !== 3) {
      throw new \Exception('Invalid card string');
    }
    $rank = Rank::fromString($matches[1]);
    $suit = Suit::fromString($matches[2]);
    return new Card(suit: $suit, rank: $rank);
  }

  public function __toString(): string {
    return $this->rank->value() . $this->suit->value() . ' (' . $this->id . ')';
  }
}