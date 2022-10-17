import React from "react";

export default function GameStats({ contract, turn }) {
    return (
        <header className="w-full font-bold text-dark-gray">
            <div className="flex items-center justify-between rounded-lg bg-light-brown py-3 px-3 shadow">
                <h2>Contract: {contract}</h2>
                <h3>Turn: {turn}</h3>
            </div>
        </header>
    );
}
