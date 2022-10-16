import {Link, Head} from '@inertiajs/inertia-react';
import React, {useEffect, useState} from 'react';
import Card from "@/Components/Card";

export default function Board() {
    return (
        <>
            <Head title="Board"/>
            <div className="relative flex flex-col items-center min-h-screen bg-[#152938] dark:bg-[#152938] sm:items-center sm:pt-0 pt-[21px] px-[16px]">
                <header className="text-[#D3DCE4] font-bold w-full">
                    <div className="flex items-center justify-between py-[12px] px-[10px] rounded-[10px] bg-[#32373C] shadow">
                        <h2>Contract: 2 Threes 1 Four</h2>
                        <h3>Turn: Nina</h3>
                    </div>
                </header>
                <div className="flex-1 w-full mt-[30px] bg-[#27577B] border border-[#32373C] rounded-[10px] grid grid-cols-4 max-h-[433px] gap-2 p-4">
                    <div>Opponent Top Three</div>
                    <div className="col-start-4">Opponent four</div>
                    <div className="">Opponent Bottom Three</div>
                    <div className="col-start-2 row-start-3">Stock Pile</div>
                    <div className="col-start-3 row-start-3">Discard Pile</div>
                    <div className="row-start-4">Player Top Three</div>
                    <div className="row-start-5">Player Bottom Three</div>
                    <div className="row-start-5 col-start-4">Player Four</div>
                </div>
                <div>
                    <menu className="w-full space-x-2 flex items-center justify-between">
                        <button className="bg-[#12B886] text-[#CBD6E1] font-bold py-[10px] px-[20px] rounded-[3px] mt-[30px] uppercase">Draw</button>
                        <button className="bg-[#12B886] text-[#CBD6E1] font-bold py-[10px] px-[20px] rounded-[3px] mt-[30px] uppercase">Discard</button>
                        <button className="bg-[#12B886] text-[#CBD6E1] font-bold py-[10px] px-[20px] rounded-[3px] mt-[30px] uppercase">Lay Cards</button>
                    </menu>
                    <div className="mt-[20px] border-2 border-pink-500 p-4 rounded text-gray-50 flex -space-x-4">
                        <Card suit={"diamond"} value={"king"} faceUp={true} />
                        <Card suit={"diamond"} value={"jack"} faceUp={true} />
                        <Card value={"joker"} faceUp={true} />
                        <Card value={"queen"} suit={"hearts"} faceUp={true} />
                        <Card value={"ace"} suit={"spades"} faceUp={true} />
                        <Card value={"2"} suit={"spades"} faceUp={true} />
                        <Card value={"3"} suit={"spades"} faceUp={true} />
                        <Card value={"4"} suit={"spades"} faceUp={true} />
                        <Card value={"5"} suit={"spades"} faceUp={true} />
                    </div>
                </div>
            </div>
        </>
    );
}