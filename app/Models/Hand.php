<?php

namespace App\Models;

use App\Enums\Suit;
use App\Providers\AppServiceProvider;

class Hand {

  public function __construct(public array $cards) {
  }

  public static function sortBySuit(array $cards): array {
    //source: https://www.deceptionary.com/aboutsuits.html
    //natural order of suits is
    // clubs < diamonds < hearts < spades
    if (empty($cards)) {
      return $cards;
    }
    // Sort arrays by count of elements
    return array_merge(...Hand::groupCardsBySuit($cards));
  }

  public static function sortByRank(array $cards = []): array {
    if (empty($cards)) {
      return $cards;
    }
    usort($cards, fn($a, $b) => $a->rank->value() <=> $b->rank->value());
    return $cards;
  }

  public static function sortBySuitThenRank(array $cards): array {
    $cardSortedBySuit = Hand::groupCardsBySuit($cards);
    return array_merge(...array_map(fn($cards) => Hand::sortByRank($cards), $cardSortedBySuit));
  }

  public function containsThree(): array {
    $cards = Hand::sortByRank($this->cards);
    $cards = array_map(fn($card) => $card->rank->value(), $cards);
    $cards = array_count_values($cards);
    $cards = array_filter($cards, fn($count) => $count >= 3);
    $cardsWithThree = array_keys($cards);
    $filteredThrees
      = array_filter($this->cards, fn($card) => in_array($card->rank->value(), $cardsWithThree));
    return Hand::sortByRank(Hand::sortBySuit(array_values($filteredThrees)));
  }

  /**
   * @param  array  $cards
   *
   * @return array
   */
  protected static function groupCardsBySuit(array $cards): array {
    $spades = [];
    $hearts = [];
    $clubs = [];
    $diamonds = [];
    foreach ($cards as $card) {
      switch ($card->suit) {
        case Suit::hearts:
          $hearts[] = $card;
          break;
        case Suit::diamonds:
          $diamonds[] = $card;
          break;
        case Suit::clubs:
          $clubs[] = $card;
          break;
        case Suit::spades:
          $spades[] = $card;
          break;
      }
    }

    $cardSuits = [
      $spades,
      $hearts,
      $clubs,
      $diamonds,
    ];
    // Sort arrays by count of elements
    usort($cardSuits, fn($a, $b) => count($b) <=> count($a));
    return $cardSuits;
  }

}
