<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;

class Card {

  public function __construct(public Suit $suit, public Rank $rank) {
  }

}