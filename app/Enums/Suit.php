<?php

namespace App\Enums;

enum Suit: string {

  case spades = '♠';

  case hearts = '♥';

  case clubs = '♣';

  case diamonds = '♦';

  public function value(): string {
    return match ($this) {
      Suit::spades => '♠',
      Suit::hearts => '♥',
      Suit::clubs => '♣',
      Suit::diamonds => '♦',
    };
  }

}