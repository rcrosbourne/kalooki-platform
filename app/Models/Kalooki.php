<?php

namespace App\Models;

use App\Events\PlayerRequestsCardFromStock;

class Kalooki {

  public function __construct(
    public array $players = [], public bool $started = FALSE,
    public array $deck = [], public array $discard = [], public array $stock = [],
  ) {
    $this->deck = count($deck) === 0 ? $this->createDeck() : $deck;
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

  public function playerRequestsCardFromStock(PlayerRequestsCardFromStock $event): void {
    $player = $event->player;
    $game = $event->game;
    $card = array_pop($game->stock);
    $player->hand->cards[] = $card;
  }

}