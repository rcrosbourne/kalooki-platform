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
                        <span>{JOKER.suit}</span>
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
                    <div className={"card faceUp"}>
                        <span>{VALUES[value]}</span>
                        <span>{SUITS[suit]}</span>
                    </div>
                </div>
            )}
        </Draggable>
    );
}
