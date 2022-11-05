import React from "react";
import { Draggable } from "react-beautiful-dnd";

const JOKER = { value: "joker", suit: "👻️" };

export interface CardProps {
    suit: "♠" | "♣" | "♥" | "♦";
    rank:
        | "A"
        | "J"
        | "Q"
        | "K"
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
    id: string;
    index: number;
    faceDown?: boolean;
}

export default function Card({ suit, rank, id, index, faceDown = false }: CardProps) {
    const getSuitColor = (suit: "♠" | "♣" | "♥" | "♦") => {
        return suit === "♥" || suit === "♦" ? "text-red-600" : "text-black";
    }
    if (faceDown) {
        return <div className="card faceDown" id={id} key={id}></div>;
    }
    if (rank === "joker") {
        return (
            <Draggable key={id} draggableId={id} index={index}>
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
                        <div className="leading-0 text-xs font-bold">{rank}</div>
                        <div className={"leading-0 -mt-1 text-xs"}>
                            {suit}
                        </div>
                    </div>
                    <div className={"-mt-1 flex w-full flex-1 items-center justify-center text-3xl " + getSuitColor(suit)}>
                        {suit}
                    </div>
                </div>
            )}
        </Draggable>
    );
}
