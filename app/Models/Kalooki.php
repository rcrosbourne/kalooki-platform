<?php

namespace App\Models;

use App\Enums\PlayerActions;
use App\Events\GameOver;
use App\Events\PlayerDiscardCardFromHand;
use App\Events\PlayerEndsTurnNotification;
use App\Events\PlayerLayDownCards;
use App\Events\PlayerRequestsCardFromDiscardPile;
use App\Events\PlayerRequestsCardFromStockPile;
use App\Events\PlayerTurnNotification;
use App\Exceptions\IllegalActionException;
use App\Facades\GameCache;
use Illuminate\Support\Str;

class Kalooki {

  protected string $id;
  protected ?Player $winner;

  public function __construct(
    ?string $id = NULL,
    public array $players = [], public bool $started = FALSE,
    public array $deck = [], public array $discard = [], public array $stock = [],
  ) {
    $this->deck = count($deck) === 0 ? $this->createDeck() : $deck;
    $this->id = $id ?: (string) Str::orderedUuid();
    $this->winner = NULL;
    GameCache::cacheGame($this);
  }

  /**
   * @return string
   */
  public function id(): string {
    return $this->id;
  }

  public function addPlayer(Player $player): void {
    $this->players[] = $player;
  }

  public function start(): void {
    if (count($this->players) < 2) {
      return;
    }
    $this->started = TRUE;
  }

  public function isStarted(): bool {
    return $this->started;
  }

  public function deal(): void {
    $this->shuffleDeck();

    $playerCount = count($this->players);
    $cardsPerPlayer = 12;
    $cardsToDeal = $playerCount * $cardsPerPlayer;
    for ($i = 0; $i < $cardsToDeal; $i++) {
      $playerIndex = $i % $playerCount;
      $this->players[$playerIndex]->hand->cards[] = array_pop($this->deck);
    }
    // Add 1 card to the discard pile.
    $this->discard[] = array_pop($this->deck);
    // Add the rest of the cards to the stockpile.
    $this->stock = $this->deck;
  }

  public function shuffleDeck(): void {
    shuffle($this->deck);
  }

  private function createDeck(): array {
    Deck::initialize();
    return array_merge(Deck::cards(), Deck::cards());
  }

  public static function fake(array $data = []): Kalooki {
    $deck
      = array_map(fn($cardString) => Card::fromString($cardString), $data['deck']
      ?? []);
    $discard
      = array_map(fn($cardString) => Card::fromString($cardString), $data['discard']
      ?? []);
    $stock
      = array_map(fn($cardString) => Card::fromString($cardString), $data['stock']
      ?? []);
    return new Kalooki(
      players: $data['players'] ?? [],
      started: $data['started'] ?? FALSE,
      deck: $deck,
      discard: $discard,
      stock: $stock,
    );
  }

  public function playerRequestsCardFromStockPile(PlayerRequestsCardFromStockPile $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    $card = array_pop($game->stock);
    $player->hand->cards[] = $card;
    $player->actionsTaken[] = PlayerActions::requestCardFromStockPile;
    $player->availableActions = $this->getAvailableActions($player, $game);
    GameCache::cacheGame($game);
  }

  /**
   * @throws \App\Exceptions\IllegalActionException
   */
  public function playerRequestsCardFromDiscardPile(PlayerRequestsCardFromDiscardPile $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    $card = array_pop($game->discard);
    if (!$card) {
      throw new IllegalActionException('No card in discard pile.');
    }
    $player->hand->cards[] = $card;
    $player->actionsTaken[] = PlayerActions::requestCardFromDiscardPile;
    $player->availableActions = $this->getAvailableActions($player, $game);
    GameCache::cacheGame($game);
  }

  /**
   * @param  \App\Events\PlayerDiscardCardFromHand  $event
   *
   * @return void
   * @throws \App\Exceptions\IllegalActionException
   */
  public function playerDiscardCardFromHand(PlayerDiscardCardFromHand $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    if (!in_array(PlayerActions::discardCardFromHand, $player->availableActions)) {
      throw new IllegalActionException('Player cannot discard card from hand.');
    }
    $card = $player->hand->removeCard($event->cardId);
    if (!$card) {
      throw new IllegalActionException('Card not in players hand.');
    }
    $game->discard[] = $card;
    $player->actionsTaken[] = PlayerActions::discardCardFromHand;
    $player->availableActions = $this->getAvailableActions($player, $game);
    GameCache::cacheGame($game);
  }

  /**
   * @throws \App\Exceptions\IllegalActionException
   */
  public function playerLayDownCards(PlayerLayDownCards $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    if(!in_array(PlayerActions::layDownCards, $player->availableActions)) {
      throw new IllegalActionException('Player cannot lay down cards.');
    }
    $this->layDownPlayersCards($player);
    $player->actionsTaken[] = PlayerActions::layDownCards;
    $player->availableActions = $this->getAvailableActions($player, $game);
    GameCache::cacheGame($game);
  }

  /**
   * @throws \App\Exceptions\IllegalActionException
   */
  public function playerEndsTurn(PlayerEndsTurnNotification $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    /** @var \App\Models\Player $player */
    $player = $gameData['player'];
    if(!in_array(PlayerActions::endTurn, $player->availableActions)) {
      throw new IllegalActionException('Player cannot end turn.');
    }
    $player->availableActions = [];
    $player->actionsTaken = [];
    $player->isTurn = FALSE;
    // If the player has not won advance to the next player.
    if($player->isWinner === TRUE) {
      $game->winner = $player;
      // send a game over event.
      broadcast(new GameOver($player->name));
    }
    else {
      // Notify the next player that it is their turn.
      $nextPlayer = $this->getNextPlayer($player, $game);
      $this->setTurn($nextPlayer->id);
    }
    GameCache::cacheGame($game);
  }

  public function setTurn(string $playerId): void {
    $gameData = GameCache::getGameState($playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    $player->isTurn = TRUE;
    // set other players turn to false.
    foreach ($game->players as $otherPlayer) {
      if ($otherPlayer->id !== $player->id) {
        $otherPlayer->isTurn = FALSE;
      }
    }
    // set player available actions, based on their hand.
    $player->availableActions = $this->getAvailableActions($player, $game);
    event(new PlayerTurnNotification($player->id));
    GameCache::cacheGame($game);
  }

  /**
   * @param  mixed  $player
   *
   * @return void
   * @throws \App\Exceptions\IllegalActionException
   */
  protected function layDownPlayersCards(mixed $player): void {
    $contract = $player->contractSatisfied();
    if (empty($contract)) {
      throw new IllegalActionException('No contract satisfied.');
    }
    $player->laidDownThrees = collect($contract['threes'])->flatten()->toArray();
    $player->laidDownFours = collect($contract['fours'])->flatten()->toArray();
    // remove laid down cards from hand
    $player->hand->cards = collect($player->hand->cards)
      ->filter(fn($card) => !in_array($card, $player->laidDownThrees) && !in_array($card, $player->laidDownFours))
      ->values()->toArray();
  }

  private function getAvailableActions(Player $player, Kalooki $game): array {
    // if a player hand is empty they already won.
    if(empty($player->hand->cards)) {
      $player->isWinner = TRUE;
      return [PlayerActions::endTurn];
    }
    $actions = [];
    $actionsAlreadyTaken = $player->actionsTaken;
    // If no actions have been taken, player can request card from stock or discard.
    if(empty($actionsAlreadyTaken)) {
      if(!empty($game->stock)) {
        $actions[] = PlayerActions::requestCardFromStockPile;
      }
      // Player can request card from discard if the discard pile is not empty,
      // and they have not laid down cards.
      if(!empty($game->discard) && empty($player->laidDownThrees) && empty($player->laidDownFours)) {
        $actions[] = PlayerActions::requestCardFromDiscardPile;
      }
      return $actions;
    }
    // If player has requested a card from the stockpile or the can discard pile a card
    // then ...
    if(in_array(PlayerActions::requestCardFromDiscardPile, $actionsAlreadyTaken) ||
      in_array(PlayerActions::requestCardFromStockPile, $actionsAlreadyTaken)) {
      // If the contract is satisfied, the player can lay down the cards.
      if(!empty($player->contractSatisfied()) && !in_array(PlayerActions::layDownCards, $actionsAlreadyTaken)) {
        $actions[] = PlayerActions::layDownCards;
      }
      // If the player has not discarded a card, and they have already requested a card
      // They can discard a card.
      if(!in_array(PlayerActions::discardCardFromHand, $actionsAlreadyTaken) &&
        (in_array(PlayerActions::requestCardFromDiscardPile, $actionsAlreadyTaken) ||
          in_array(PlayerActions::requestCardFromStockPile, $actionsAlreadyTaken))) {
        $actions[] = PlayerActions::discardCardFromHand;
      }
    }
    // If a player has drawn a card and discarded a card they can end their turn
    if(in_array(PlayerActions::discardCardFromHand, $actionsAlreadyTaken) &&
      (in_array(PlayerActions::requestCardFromDiscardPile, $actionsAlreadyTaken) ||
        in_array(PlayerActions::requestCardFromStockPile, $actionsAlreadyTaken))) {
      $actions[] = PlayerActions::endTurn;
    }
    return $actions;
  }

  private function getNextPlayer(Player $player, Kalooki $game): Player {
    //get the players index in the game.
    $playerIndex = collect($game->players)->search(fn(Player $p) => $p->id === $player->id);
    // if the player is the last player in the game, return the first player.
    if($playerIndex === count($game->players) - 1) {
      return $game->players[0];
    }
    // return the next player.
    return $game->players[$playerIndex + 1];
  }

}