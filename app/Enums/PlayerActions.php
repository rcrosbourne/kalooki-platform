<?php

namespace App\Enums;

enum PlayerActions: string {
  case requestCardFromDiscardPile = 'requestCardFromDiscardPile';
  case requestCardFromStockPile = 'requestCardFromStockPile';
  case layDownCards = 'layDownCards';
  case tackOnCards = 'tackOnCards';
  case discardCardFromHand = 'discardCardFromHand';
  case endTurn = 'endTurn';
}