<?php

namespace App\Models;

use App\Enums\PlayerActions;
use App\Events\PlayerDiscardCardFromHand;
use App\Events\PlayerLayDownCards;
use App\Events\PlayerRequestsCardFromDiscardPile;
use App\Events\PlayerRequestsCardFromStockPile;
use App\Events\PlayerTurnNotification;
use App\Exceptions\IllegalActionException;
use App\Facades\GameCache;
use Illuminate\Support\Str;

class Kalooki {

  protected string $id;

  public function __construct(
    ?string $id = NULL,
    public array $players = [], public bool $started = FALSE,
    public array $deck = [], public array $discard = [], public array $stock = [],
  ) {
    $this->deck = count($deck) === 0 ? $this->createDeck() : $deck;
    $this->id = $id ?: (string) Str::orderedUuid();
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
    // Add the rest of the cards to the stock pile.
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
    $card = $player->hand->removeCard($event->cardId);
    if (!$card) {
      throw new IllegalActionException('Card not in players hand.');
    }
    $game->discard[] = $card;
    GameCache::cacheGame($game);
  }

  /**
   * @throws \App\Exceptions\IllegalActionException
   */
  public function playerLayDownCards(PlayerLayDownCards $event): void {
    $gameData = GameCache::getGameState($event->playerId);
    $game = $gameData['game'];
    $player = $gameData['player'];
    $this->layDownPlayersCards($player);
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
    $actions = [];
    // if a player has their contract satisfied, they can lay down their cards.
    if (!empty($player->contractSatisfied())) {
      $actions[] = PlayerActions::layDownCards;
    }
    // if there are cards in the discard pile, a player can request a card from it.
    if (!empty($game->discard)) {
      $actions[] = PlayerActions::requestCardFromDiscardPile;
    }
    // if there are cards in the stockpile, a player can request a card from it.
    if (!empty($game->stock)) {
      $actions[] = PlayerActions::requestCardFromStockPile;
    }
    // if a player has cards in their hand, they can discard a card.
    if (!empty($player->hand->cards)) {
      $actions[] = PlayerActions::discardCardFromHand;
    }
    return $actions;
  }

}