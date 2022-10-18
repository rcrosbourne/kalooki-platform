import React from "react";
import { Draggable } from "react-beautiful-dnd";
import { useId } from "@mantine/hooks";

const SUITS = { spades: "♠", clubs: "♣", hearts: "♥", diamond: "♦" };
const VALUES = {
    ace: "A",
    jack: "J",
    queen: "Q",
    king: "K",
    "10": 10,
    "9": 9,
    "8": 8,
    "7": 7,
    "6": 6,
    "5": 5,
    "4": 4,
    "3": 3,
    "2": 2,
};
const JOKER = { value: "joker", suit: "👻️" };

export interface CardProps {
    suit: string;
    value: string;
    faceUp: boolean;
    index: number;
}

export default function Card({ suit, value, faceUp, index }: CardProps) {
    const id = useId();
    if (!faceUp) {
        return <div className="card faceDown" id={id} key={id}></div>;
    }
    if (value === "joker") {
        return (
            <Draggable key={id} index={index} draggableId={id}>
                {(provided, snapshot) => (
                    <div
                        className={
                            "card joker" +
                            (snapshot.isDragging ? " cardDragging" : "")
                        }
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                        ref={provided.innerRef}>
                        <span className="text-3xl">{JOKER.suit}</span>
                    </div>
                )}
            </Draggable>
        );
    }
    return (
        <Draggable key={id} draggableId={id} index={index}>
            {(provided, snapshot) => (
                <div
                    className={
                        "card" + (snapshot.isDragging ? " cardDragging" : "")
                    }
                    {...provided.draggableProps}
                    {...provided.dragHandleProps}
                    ref={provided.innerRef}>
                    {/*<div className={"card faceUp"}>*/}
                    {/*    <span>{VALUES[value]}</span>*/}
                    {/*    <span>{SUITS[suit]}</span>*/}
                    {/*</div>*/}
                    {/*<div className="flex flex-col w-full p-2">*/}
                    {/*    <div className="flex flex-col justify-start border-2 border-pink-300">*/}
                    {/*        <span className="text-sm">{VALUES[value]}</span>*/}
                    {/*        <span className="text-sm -mt-0.5">{SUITS[suit]}</span>*/}
                    {/*    </div>*/}
                    {/*    <div className="flex-1 flex items-center justify center w-full border-2 border-green-400"><span className="text-3xl">{SUITS[suit]}</span></div>*/}
                    {/*</div>*/}
                    <div className="w-full pl-0.5">
                        <div className="leading-0 text-xs font-bold">{VALUES[value]}</div>
                        <div className="leading-0 -mt-1 text-xs">
                            {SUITS[suit]}
                        </div>
                    </div>
                    <div className="-mt-1 flex w-full flex-1 items-center justify-center text-3xl">
                        {SUITS[suit]}
                    </div>
                </div>
            )}
        </Draggable>
    );
}
