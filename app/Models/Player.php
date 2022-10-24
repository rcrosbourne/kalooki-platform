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

  public function contractSatisfied():bool {
    // contract is 2 threes 1 four
    $threes = 0;
    $fours = 0;
    // Detect how many threes and fours are in the hand
    $threes = $this->hand->containsThree();
    if(count($threes) >= 2) {
      // remove the 3s from the hand

      $fours = $this->hand->containsFour();
    }
    $fours = $this->hand->containsFour();
//    $intersect = array_intersect($threes, $fours);

//    $threes = $this->hand->containsThree();
//    $fours = $this->hand->containsFour();
    // If there are 2 threes and 1 four then the contract is satisfied
    // cards belonging to a three cannot belong to a four

    return $threes >= 2 && $fours >= 1;
  }
}