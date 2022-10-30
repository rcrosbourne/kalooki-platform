<?php

namespace App\Models;


use App\Events\ListUpdate;
use App\Events\PlayerRequestsCardFromStock;
use Illuminate\Auth\Events\Registered;

class Player {

  public function __construct(public string $name, public ?Hand $hand = NULL) {
    $this->hand = $hand ?: new Hand([]);
  }

  /**
   * @param  array  $data
   *
   * @return \App\Models\Player
   * @throws \Exception
   */
  public static function fake(array $data = []): Player {
    $hand
      = new Hand(array_map(fn($cardString) => Card::fromString($cardString), $data['hand']));
    return new Player(
      name: $data['name'] ?? 'Player',
      hand: $hand,
    );
  }

  public function drawFromStock(Kalooki &$game): void {
    event(new PlayerRequestsCardFromStock($this, $game));
  }

  /**
   * @return array
   */
  public function contractSatisfied(): array {
    // contract is 2 threes 1 four
    // this is the array we will mutate
    $cards = $this->hand->cards;
    $solution = [];
    // Detect how many threes and fours are in the hand
    $threes = Hand::containsThree($cards);
    $fours = Hand::containsFour($cards);
    if (count($threes) < 2 || count($fours) < 1) {
      return [];
    }

    //let's iterate over the fours and check if the card exist in the three
    $sequence = [];
    foreach ($fours as $four) {
      foreach ($four as $card) {
        // If the sequence already contains four cards we can stop.
        // The objective is to find the minimum amount of cards needed to satisfy the fours.
        if (count($sequence) >= 4) {
          break;
        }
        // If a card is found in threes, but the amount of threes is less than 3,
        // we can't use it, otherwise we add it to the sequence.
        $sequence = Hand::containsCard($threes, $card) ? []
          : array_merge($sequence, [$card]);
      }
    }
    // If the sequence is not 4 cards long, we can't use it.
    if (count($sequence) === 4) {
      // Attempt to tack on leftover cards
      // use array values to reset indexes
      $leftOverCards
        = array_values(array_diff($cards, $sequence, collect($threes)->flatten()->toArray()));
      // If we have leftover cards, see if we can add them to the sequence.
      $numberOfLeftOverCards = count($leftOverCards);
      $numberOfIterations = 0;
      while (TRUE) {
        // If we have no more cards to add, we can stop.
        if ($numberOfIterations >= $numberOfLeftOverCards) {
          break;
        }
        // If we can add a card to the sequence, we do so.
        if (Hand::canAddCardToFours($sequence, $leftOverCards[$numberOfIterations]) !== -1) {
          // If we can add a card to front of the sequence we do so.
            $sequence[] = $leftOverCards[$numberOfIterations];
            unset($leftOverCards[$numberOfIterations]);
        }
        $numberOfIterations++;
      }
      $solution['fours'] = $sequence;
      $solution['threes'] = $threes;
//      return $solution;
    }
    return $solution;
  }

}