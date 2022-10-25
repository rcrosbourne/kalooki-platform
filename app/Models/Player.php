<?php

namespace App\Models;

class Player {

  public function __construct(public string $name, public ?Hand $hand = null) {
    $this->hand = $hand ?: new Hand([]);
  }

  /**
   * @param  array  $data
   *
   * @return \App\Models\Player
   * @throws \Exception
   */
  public static function fake(array $data = []): Player {
    $hand = new Hand(array_map(fn($cardString) => Card::fromString($cardString), $data['hand']));
    return new Player(
      name: $data['name'] ?? 'Player',
      hand: $hand,
    );
  }

  public function contractSatisfied():array {
    // contract is 2 threes 1 four
    // this is the array we will mutate
    $cards = $this->hand->cards;
    $solution = [];
    // Detect how many threes and fours are in the hand
    $threes = Hand::containsThree($cards);
    $fours = Hand::containsFour($cards);
    if(count($threes) < 2 || count($fours) < 1) {
      return [];
    }

    //let's iterate over the fours and check if the card exist in the three
    $sequence = [];
    foreach ($fours as $four) {
      foreach($four as $card) {
        if(count($sequence) >= 4) {
          break;
        }
        $sequence = Hand::containsCard($threes, $card) ? [] : array_merge($sequence, [$card]);
      }
    }
    if(count($sequence) === 4) {
      $solution['fours'] = $sequence;
      $solution['threes'] = $threes;
    }
    return $solution;
  }
}