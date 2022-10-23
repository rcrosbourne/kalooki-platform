<?php

namespace App\Enums;

enum Rank: string {

  case two = '2';
  case three = '3';
  case four = '4';
  case five = '5';
  case six = '6';
  case seven = '7';
  case eight = '8';
  case nine = '9';
  case ten = '10';
  case jack = 'J';
  case queen = 'Q';
  case king = 'K';
  case ace = 'A';

  public function value(): int
    {
        return match($this)
        {
            self::two => 2,
            self::three => 3,
            self::four => 4,
            self::five => 5,
            self::six => 6,
            self::seven => 7,
            self::eight => 8,
            self::nine => 9,
            self::ten => 10,
            self::jack => 11,
            self::queen => 12,
            self::king => 13,
            self::ace => 14,
        };
    }
}