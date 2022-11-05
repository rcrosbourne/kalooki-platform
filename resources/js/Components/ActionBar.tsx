import React from "react";
import GameButton from "@/Components/GameButton";

export default function ActionBar({disableActions= false}) {
    return (
        <menu className="flex w-full">
            <ul className="grid w-full grid-cols-3 place-content-stretch gap-2">
                <li>
                    <GameButton processing={disableActions}>Draw</GameButton>
                </li>
                <li>
                    <GameButton processing={disableActions}>Discard</GameButton>
                </li>
                <li>
                    <GameButton processing={disableActions}>Lay Cards</GameButton>
                </li>
            </ul>
        </menu>
    );
}
