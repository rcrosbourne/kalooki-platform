import { Link, Head } from "@inertiajs/inertia-react";
import React, { useEffect, useState } from "react";
import Card from "@/Components/Card";
import { DragDropContext, Droppable } from "react-beautiful-dnd";
import { useListState, useId } from "@mantine/hooks";
import GameStats from "@/Components/GameStats";
import Meld from "@/Components/Meld";
import ActionBar from "@/Components/ActionBar";

const cards = [
    <Card suit={"diamond"} value={"king"} faceUp={true} index={0} key={0} />,
    <Card suit={"diamond"} value={"jack"} faceUp={true} index={1} key={1} />,
    <Card value={"joker"} faceUp={true} index={2} key={2} />,
    <Card value={"queen"} suit={"hearts"} faceUp={true} index={3} key={3} />,
    <Card value={"ace"} suit={"spades"} faceUp={true} index={4} key={4} />,
    <Card value={"2"} suit={"spades"} faceUp={true} index={5} key={5} />,
    <Card value={"3"} suit={"spades"} faceUp={true} index={6} key={6} />,
    <Card value={"4"} suit={"spades"} faceUp={true} index={7} key={7} />,
    <Card value={"5"} suit={"spades"} faceUp={true} index={8} key={8} />,
];
export default function Board() {
    const [playerHand, playerHandHandler] = useListState(cards);
    return (
        <>
            <Head title="Board" />
            <div className="relative flex min-h-screen flex-col items-center overflow-hidden bg-dark-blue px-4 pt-5 dark:bg-dark-blue sm:items-center sm:pt-0">
                <GameStats contract={"2 Threes 1 Fours"} turn={"Nina"} />
                <DragDropContext
                    onDragEnd={({ source, destination }) =>
                        playerHandHandler.reorder({
                            from: source.index,
                            to: destination.index,
                        })
                    }>
                    <div className="mt-8 grid max-h-[433px] w-full flex-1 grid-cols-4 gap-2 rounded-xl border border-light-brown bg-light-blue p-4">
                        <Meld
                            droppableId={"opponentTopThree"}
                            cards={playerHand.slice(0, 3)}
                        />
                        <Meld
                            droppableId={"opponentFour"}
                            cards={playerHand.slice(3, 7)}
                            className="col-span-2 col-start-3"
                        />
                        <Meld
                            droppableId={"opponentBottomThree"}
                            cards={playerHand.slice(2, 5)}
                        />

                        <div className="col-start-2 row-start-3 grid">
                            {[12, 13, 14, 15].map((number, index) => (
                                <div className="col-start-1 row-start-1">
                                    <Card
                                        faceUp={false}
                                        index={number}
                                        key={Math.floor(number * Math.PI)}
                                    />
                                </div>
                            ))}
                        </div>
                        <Droppable droppableId="discardPile" direction="none">
                            {(provided) => (
                                <div
                                    {...provided.droppableProps}
                                    ref={provided.innerRef}
                                    className="col-start-3 row-start-3 grid">
                                    {playerHand
                                        .slice(3, 7)
                                        .map((card, index) => (
                                            <div
                                                key={index}
                                                className="col-start-1 row-start-1 flex-1">
                                                {card}
                                            </div>
                                        ))}
                                    {provided.placeholder}
                                </div>
                            )}
                        </Droppable>
                        <Meld
                            droppableId={"playerTopThree"}
                            cards={playerHand.slice(0, 3)}
                            className={"row-start-4"}
                        />

                        <Meld
                            droppableId={"playerBottomThree"}
                            cards={playerHand.slice(3, 7)}
                            className={"row-start-5"}
                        />

                        <Meld
                            droppableId={"playerFour"}
                            cards={playerHand.slice(3, 7)}
                            className={"col-span-2 col-start-3 row-start-5"}
                        />
                    </div>
                    <div className="mt-[30px]">
                        <ActionBar />
                        <Meld
                            droppableId={"playerHand"}
                            className="mt-5 flex items-center justify-center p-4 text-gray-50"
                            cards={playerHand}
                        />
                    </div>
                </DragDropContext>
            </div>
        </>
    );
}
