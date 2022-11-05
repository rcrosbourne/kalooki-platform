import React from "react";

export default function GameStats({ opponent, turn }) {
    return (
        <header className="w-full font-bold text-dark-gray">
            <div className="flex items-center justify-between rounded-lg bg-light-brown py-3 px-3 shadow">
                <h2>Opponent: {opponent}</h2>
                <h3>Turn: {turn}</h3>
            </div>
        </header>
    );
}
