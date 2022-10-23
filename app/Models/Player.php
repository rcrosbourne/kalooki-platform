<?php

namespace App\Models;

class Player {

  public function __construct(public string $name, public array $hand = []) {}

}