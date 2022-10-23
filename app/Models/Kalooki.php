<?php

namespace App\Models;

class Kalooki {

    public function __construct(public array $players = [], public bool $started = false) {}

    public function addPlayer(Player $player): void {
      $this->players[] = $player;
    }

    public function start(): void {
      if (count($this->players) < 2) {
        return;
      }
      $this->started = true;
    }

    public function isStarted(): bool {
      return $this->started;
    }

}