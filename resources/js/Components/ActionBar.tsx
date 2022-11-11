import React from "react";
import GameButton from "@/Components/GameButton";

interface Props {
  disableActions: boolean;
  availableActions: string[];
  onTurnEnd: () => void;
  onLayDownCards: () => void;
  onTackOnCards: () => void;
}

export default function ActionBar({ disableActions = false, availableActions, onTurnEnd, onLayDownCards, onTackOnCards }: Props) {
  const canDraw = !disableActions && (availableActions.includes("requestCardFromDiscardPile") || availableActions.includes("requestCardFromStockPile"));
  const canLayCards = !disableActions && (availableActions.includes("layDownCards"));
  const canDiscard = !disableActions && (availableActions.includes("discardCardFromHand"));
  const canEndTurn = !disableActions && (availableActions.includes("endTurn"));
  const canTackOnCards = !disableActions && (availableActions.includes("tackOnCards"));
  // Once a player has laid down cards, they can only tack on cards.
  const canOnlyTackOnCards = !canLayCards && canTackOnCards;
  return (
    <menu className="flex w-full">
      <ul className="grid w-full grid-cols-2 place-content-stretch gap-2">
        <li>
          <GameButton processing={!canDraw}>Draw</GameButton>
        </li>
        <li>
          <GameButton processing={!canLayCards && !canTackOnCards} onClick={canOnlyTackOnCards ? onTackOnCards : onLayDownCards}>{canOnlyTackOnCards ? "Tack On" : "Lay Down"}</GameButton>
        </li>
        <li>
          <GameButton processing={!canDiscard}>Discard</GameButton>
        </li>
        <li>
          <GameButton processing={!canEndTurn} onClick={onTurnEnd}>End Turn</GameButton>
        </li>
      </ul>
    </menu>
  );
}
