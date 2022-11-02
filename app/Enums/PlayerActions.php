<?php

namespace App\Enums;

enum PlayerActions {
  case requestCardFromDiscardPile;
  case requestCardFromStockPile;
  case layDownCards;
  case discardCardFromHand;
  case endTurn;
}