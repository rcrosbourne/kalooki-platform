import React from "react";

export default function GameStats({ contract, turn }) {
    return (
        <header className="w-full font-bold text-[#D3DCE4]">
            <div className="flex items-center justify-between rounded-[10px] bg-[#32373C] py-[12px] px-[10px] shadow">
                <h2>Contract: {contract}</h2>
                <h3>Turn: {turn}</h3>
            </div>
        </header>
    );
}
