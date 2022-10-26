<?php

namespace App\Models;

use App\Enums\Suit;

class Hand {

  /**
   * Constructor
   *
   * @param  array  $cards
   */
  public function __construct(public array $cards) {
  }

  /**
   * Sort cards according to suit as described at the location below:
   * The natural order of suits is: Spades, Hearts, Clubs, Diamonds.
   *
   * @source https://www.deceptionary.com/aboutsuits.html
   * @param  array  $cards
   *
   * @return array
   */
  public static function sortBySuit(array $cards): array {
    if (empty($cards)) {
      return $cards;
    }
    // Sort arrays by count of elements
    return array_merge(...Hand::groupCardsBySuit($cards));
  }

  /**
   * Sort cards according to rank. For this instance we are using a High Ace
   * only The natural order of ranks is: 2, 3, 4, 5, 6, 7, 8, 9, 10, Jack,
   * Queen, King, Ace.
   *
   * @param  array  $cards
   *
   * @return array
   */
  public static function sortByRank(array $cards = []): array {
    if (empty($cards)) {
      return $cards;
    }
    usort($cards, fn($a, $b) => $a->rank->value() <=> $b->rank->value());
    return $cards;
  }

  /**
   * Sort cards according to suit then subsequently by rank.
   *
   * @param  array  $cards
   *
   * @return array
   */
  public static function sortBySuitThenRank(array $cards): array {
    $cardSortedBySuit = Hand::groupCardsBySuit($cards);
    return array_merge(...array_map(fn($cards) => Hand::sortByRank($cards), $cardSortedBySuit));
  }

  /**
   * Sort cards according to rank then subsequently by suit.
   *
   * @param  array  $cards
   *
   * @return array
   */
  public static function sortByRankThenOrderRankBySuit(array $cards): array {
    return Hand::sortByRank(Hand::sortBySuit(array_values($cards)));
  }

  /**
   * Returns an array of threes if it exists, or empty array if it does not.
   * A "three" is a set of three or more cards of the same rank, such as 5-5-5
   * or K-K-K-K-K. The array is sorted by Rank then Suit. E.g [K♠-K♣-K♥,
   * 5♦-5♠-5♥] => [5♠-5♥-5♦, K♠-K♥-K♣]
   *
   * @source: https://www.pagat.com/rummy/kaluki2.html
   *
   * @param  array  $cards
   *
   * @return array
   */
  public static function containsThree(array $cards = []): array {
    // Sort cards by rank so all cards of the same rank are next to each other
    $sortedCards = Hand::sortByRank($cards);
    $cardRanksThatAreThrees
      = self::findRanksThatContainThreeOrMoreCards($sortedCards);
    // filter cards that are threes from list of cards
    if (empty($cardRanksThatAreThrees)) {
      return [];
    }
    $threes = [];
    foreach (array_keys($cardRanksThatAreThrees) as $cardRank) {
      $threes[]
        = Hand::sortByRankThenOrderRankBySuit(
        array_filter($cards, fn($card) => $card->rank->value() === $cardRank)
      );
    }
    return $threes;
  }

  /**
   * Returns an array of fours if it exists, or empty array if it does not.
   * A "four" is a run of four or more consecutive cards in the same suit, such
   * as 8♥-9♥-10♥-J♥-Q♥. The list is sorted by suit order then rank. E.g
   * [8♣-9♣-10♣-J♣-Q♣, 8♠-9♠-10♠-J♠-Q♠ ] => [8♠-9♠-10♠-J♠-Q♠, 8♣-9♣-10♣-J♣-Q♣]
   *
   * @source https://www.pagat.com/rummy/kaluki2.html
   * @param  array  $cards
   *
   * @return array
   */
  public static function containsFour(array $cards = []): array {
    // Sort cards by suit so all cards of the same suit are next to each other
    $sortedCards = Hand::sortBySuit($cards);
    // Find the count of cards per suit
    $sortedCards = self::findSuitsThatContainFourOrMoreCards($sortedCards);

    // empty then no fours exist, we can return early
    if (empty($sortedCards)) {
      return [];
    }
    // Sequence Array
    $sequence = [];
    foreach ($sortedCards as $suit => $amount) {
      $fours = array_filter($cards, function ($c) use ($suit) {
        return $c->suit->value() === Suit::fromString($suit)->value();
      });

      $sequence[] = self::returnSequenceOfFourOrMore($fours);
      //      $sequence
      //        = array_merge($sequence, $this->returnSequenceOfFourOrMore($fours));
    }
    return $sequence;
  }

  /**
   * Utility function that returns an array of cards that are in sequence.
   * E.g [8♣-9♣-10♣-J♣-Q♣-8♠-2♠-3♠-J♠-Q♠ ] => [8♣-9♣-10♣-J♣-Q♣]
   *
   * @param  array  $cards
   *
   * @return array
   */
  protected static function returnSequenceOfFourOrMore(array $cards): array {
    $sequence = [];
    // Sort cards by rank
    $foursSorted = Hand::sortByRank($cards);
    // remove duplicates
    foreach ($foursSorted as $key => $card) {
      if (isset($foursSorted[$key + 1])
        && $card->rank->value() === $foursSorted[$key + 1]->rank->value()
      ) {
        unset($foursSorted[$key + 1]);
      }
    }
    $foursSorted = array_values($foursSorted);
    // check if a sequence exists
    foreach ($foursSorted as $key => $card) {
      // if we already found a sequence of 4, return it

      if ($key === 0) {
        $sequence[] = $card;
        continue;
      }
      if ($card->rank->value() !== $foursSorted[$key - 1]->rank->value() + 1) {
        // if it's not the last element start a new sequence.
        if (count($sequence) >= 4) {
          return $sequence;
        }
        if ($key !== count($foursSorted) - 1) {
          $sequence = [$card];
        }
      } else {
        $sequence[] = $card;
      }
    }
    return count($sequence) >= 4 ? $sequence : [];
  }

  /**
   * Utility function that groups cards by suit.
   *
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

  /**
   * Utility function that find card ranks that contain three or more cards.
   *
   * @param  array  $cards
   *
   * @return array
   */
  protected static function findRanksThatContainThreeOrMoreCards(array $cards): array {
    // Get card values and count the number of times each value appears
    $countOfCardsPerRank
      = array_count_values(array_map(fn($card) => $card->rank->value(), $cards));
    // Filter out cards that don't appear 3 or more times
    return array_filter($countOfCardsPerRank, fn($count) => $count >= 3);
  }

  /**
   * Utility function that find card suits that contain four or more cards.
   *
   * @param  array  $cards
   *
   * @return array
   */
  protected static function findSuitsThatContainFourOrMoreCards(array $cards): array {
    $countOfCardsPerSuit
      = array_count_values(array_map(fn($card) => $card->suit->value(), $cards));
    // Filter out suits that are not four or more
    return array_filter($countOfCardsPerSuit, fn($count) => $count >= 4);
  }

  public static function removeCards(array $cards, array $threesOrFours): array {
    foreach ($threesOrFours as $threesOrFour) {
      foreach ($threesOrFour as $card) {
        $key = array_search($card, $cards);
        unset($cards[$key]);
      }
    }
    return $cards;
  }

  public static function containsCard(array &$threesOrFours, Card $card): bool {
    foreach ($threesOrFours as $index => $threesOrFour) {
      // if the card is in list of threes and the number of threes is less than 3
      // then we can't use it and return true
      if (in_array($card, $threesOrFour)) {
        if (count($threesOrFour) <= 3) {
          return TRUE;
        }
        // otherwise we can use it. We remove the card from the list of threes and return false
        // remove the card and return false
        $key = array_search($card, $threesOrFour);
        unset($threesOrFours[$index][$key]);
        return FALSE;
      }
    }
    return FALSE;
  }

  public static function canAddCardToThrees(array &$threes, Card $card): int {
    foreach ($threes as $index => $three) {
      if ($card->rank->value() === $three[0]->rank->value()) {
        return $index;
      }
    }
    return -1;
  }

  public static function canAddCardToFours(array $sequence, card $card): int {
    // can card be added to the end of the sequence
    if ($card->rank->value() === $sequence[count($sequence) - 1]->rank->value()
      + 1
    ) {
      return count($sequence);
    }
    return -1;
  }

}
