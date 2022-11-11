<?php

namespace App\Models;


use App\Enums\Rank;
use App\Events\PlayerDiscardCardFromHand;
use App\Events\PlayerEndsTurnNotification;
use App\Events\PlayerLayDownCards;
use App\Events\PlayerReorderHand;
use App\Events\PlayerRequestsCardFromDiscardPile;
use App\Events\PlayerRequestsCardFromStockPile;
use App\Events\PlayerTackOnCards;
use App\Events\PlayerTurnNotification;
use Illuminate\Support\Str;

class Player {


  public function __construct(
    public string $name = "", public ?Hand $hand = NULL, public ?string $id = NULL,
    public array $laidDownThrees = [],
    public array $topThrees = [],
    public array $bottomThrees = [],
    public array $laidDownFours = [], public bool $isTurn = FALSE, public array $availableActions = [],
    public array $actionsTaken = [],
    public bool $isWinner = FALSE,
  ) {
    $this->hand = $hand ?: new Hand([]);
    $this->id = $id ?: (string) Str::orderedUuid();
    $this->name = $name ?: 'Player ' . $this->id;
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
      id: $data['id'] ?? NULL,
    );
  }

  public function drawFromStockPile(): void {
    event(new PlayerRequestsCardFromStockPile($this->id));
  }

  public function drawFromDiscardPile(): void {
    event(new PlayerRequestsCardFromDiscardPile($this->id));
  }

  public function discardFromHand(Card $card): void {
    event(new PlayerDiscardCardFromHand($this->id, $card->id));
  }

  public function reorderHand($from, $to): void {
    event(new PlayerReorderHand($this->id, $from, $to));
  }

  public function layDownCards(): void {
    event(new PlayerLayDownCards($this->id));
  }

  public function tackOnCards (): void {
    event(new PlayerTackOnCards($this->id));
  }

  public function endTurn(): void {
    event(new PlayerEndsTurnNotification($this->id));
  }

  public function availableActions(): array {
    return $this->availableActions;
  }

  public function onPlayerTurnNotification(PlayerTurnNotification $event): void {
    //    $this->isTurn = $event->playerId === $this->id;
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
        = Hand::sortBySuitThenRank(array_values(array_diff($cards, $sequence, collect($threes)->flatten()->toArray())));
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

  public function canTackOnCards(): array {
    //If no cards have been laid down return
    if (empty($this->laidDownThrees) && empty($this->laidDownFours)) {
      return [];
    }
    $initialSequence = $this->laidDownFours;
    $initialHand = $this->hand->cards;
    $currentCardIndex = 0;
    while(TRUE) {
      // if we have no cards left to tack on, we can stop.
      if(empty($initialHand)) {
        break;
      }
      // if we are already at the end of the sequence, we can stop.
      if($currentCardIndex >= count($initialHand)) {
        break;
      }
      // If we can add a card to the sequence, we do so.
      if ($this->canTackOnToSequence($initialSequence, $initialHand[$currentCardIndex])) {
        // If we can add a card to front of the sequence we do so.
        unset($initialHand[$currentCardIndex]);
        //reset the card index
        $initialHand = array_values($initialHand);
        $currentCardIndex = 0;
      } else {
        $currentCardIndex++;
      }
    }
    $cards = [];

    if(count(array_diff($initialSequence, $this->laidDownFours)) > 0) {
     $cards['fours'] = $initialSequence;
    }
    // We should probably reassign the hand here.
    /** @var Card $lastCardInTopThree */
    $lastCardInTopThree = end($this->topThrees);
    /** @var Card $lastCardInBottomThree */
    $lastCardInBottomThree = end($this->bottomThrees);
    $cardsForTopThree = array_values(array_filter($initialHand, fn(Card $card) => $card->rank->value() === $lastCardInTopThree->rank->value()));
    $cardsForBottomThree = array_values(array_filter($initialHand, fn(Card $card) => $card->rank === $lastCardInBottomThree->rank));
//    // return all the cards that can be tacked on
    if(!empty($cardsForTopThree)) {
      $cards['topThrees'] = array_merge($this->topThrees, $cardsForTopThree);
      // remove card from initialHand
      $initialHand = array_values(array_diff($initialHand, $cardsForTopThree));
    }
    if(!empty($cardsForBottomThree)) {
      $cards['bottomThrees'] = array_merge($this->bottomThrees, $cardsForBottomThree);
      // remove card from initialHand
      $initialHand = array_values(array_diff($initialHand, $cardsForBottomThree));
    }
    if(count(array_diff($this->hand->cards, $initialHand)) > 0) {
      $cards['hand'] = $initialHand;
    }
    return $cards;
  }
  private function canTackOnToSequence(array &$sequence, Card $card): bool {
    // if the last card in four is an ACE we can tack at the start of the sequence
    $sequenceDirection = 1;
    $lastCardInFour = end($sequence);
    if (end($sequence)->rank === Rank::ace) {
      $sequenceDirection = -1;
      $lastCardInFour = reset($sequence);
    }
    //if the card can be added to the end of the sequence
    // then we can tack on to the sequence
    if($card->rank->value() === $lastCardInFour->rank->value() + $sequenceDirection && $card->suit === $lastCardInFour->suit) {
      if($sequenceDirection == 1) {
        $sequence[] = $card;
      } else {
        array_unshift($sequence, $card);
      }
    }
    return $card->rank->value() === $lastCardInFour->rank->value() + $sequenceDirection && $card->suit === $lastCardInFour->suit;
  }

}