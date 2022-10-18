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
    suit: "spades" | "clubs" | "hearts" | "diamond";
    value:
        | "ace"
        | "jack"
        | "queen"
        | "king"
        | "10"
        | "9"
        | "8"
        | "7"
        | "6"
        | "5"
        | "4"
        | "3"
        | "2"
        | "joker";
    index: number;
    faceDown?: boolean;
}

export default function Card({ suit, value, index, faceDown = false }: CardProps) {
    const getSuitColor = (suit: "spades" | "clubs" | "hearts" | "diamond") => {
        return suit === "hearts" || suit === "diamond" ? "text-red-600" : "text-black";
    }

    const id = useId();
    if (faceDown) {
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
                    <div className={"w-full pl-0.5 " + getSuitColor(suit)}>
                        <div className="leading-0 text-xs font-bold">{VALUES[value]}</div>
                        <div className={"leading-0 -mt-1 text-xs"}>
                            {SUITS[suit]}
                        </div>
                    </div>
                    <div className={"-mt-1 flex w-full flex-1 items-center justify-center text-3xl " + getSuitColor(suit)}>
                        {SUITS[suit]}
                    </div>
                </div>
            )}
        </Draggable>
    );
}
