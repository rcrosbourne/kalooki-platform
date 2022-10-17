import React from "react";
import GameButton from "@/Components/GameButton";

export default function ActionBar() {
    return (
        <menu className="flex w-full">
            <ul className="grid w-full grid-cols-3 place-content-stretch gap-2">
                <li>
                    <GameButton>Draw</GameButton>
                </li>
                <li>
                    <GameButton>Discard</GameButton>
                </li>
                <li>
                    <GameButton>Lay Cards</GameButton>
                </li>
            </ul>
        </menu>
    );
}
