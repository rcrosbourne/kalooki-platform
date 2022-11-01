<?php

namespace App\Enums;

enum PlayerActions {
  case layDownCards;
  case requestCardFromDiscardPile;
  case requestCardFromStockPile;
  case discardCardFromHand;
}