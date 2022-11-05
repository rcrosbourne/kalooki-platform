<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;
use App\Exceptions\NotEnoughCardsException;
use Illuminate\Support\Str;

class Deck {

  private static array $cards;


  public static function initialize(): void {
    $suits = [Suit::clubs, Suit::diamonds, Suit::hearts, Suit::spades];
    $ranks = [
      Rank::ace,
      Rank::two,
      Rank::three,
      Rank::four,
      Rank::five,
      Rank::six,
      Rank::seven,
      Rank::eight,
      Rank::nine,
      Rank::ten,
      Rank::jack,
      Rank::queen,
      Rank::king,
    ];
    $cards = [];
    foreach ($suits as $suit) {
      foreach ($ranks as $rank) {
        $cards[] = new Card(suit: $suit, rank: $rank, id: self::generateUniqueId($cards));
      }
    }
    self::$cards = $cards;
  }

  public static function cards(): array {
    return self::$cards;
  }

  public static function shuffle(): void {
    shuffle(self::$cards);
  }

  public static function initializeAndShuffle(): void {
    self::initialize();
    self::shuffle();
  }

  /**
   * @throws \App\Exceptions\NotEnoughCardsException
   */
  public static function deal(int $numberOfCards): array {
    if ($numberOfCards > count(self::$cards)) {
      throw new NotEnoughCardsException();
    }
    return array_splice(self::$cards, 0, $numberOfCards);
  }

  private static function generateUniqueId(array $cards): string {
    $id = (string) Str::orderedUuid();
    foreach ($cards as $card) {
      if ($card->id === $id) {
        return self::generateUniqueId($cards);
      }
    }
    return $id;
  }

}