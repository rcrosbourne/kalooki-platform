import { Link, Head } from "@inertiajs/inertia-react";
import React, { useEffect, useState } from "react";
import Card from "@/Components/Card";
import { DragDropContext, Droppable } from "react-beautiful-dnd";
import { useListState, useId } from "@mantine/hooks";
import GameStats from "@/Components/GameStats";
import Meld from "@/Components/Meld";
import ActionBar from "@/Components/ActionBar";

interface Props {
    gameId: string;
    player: {id: string, name: string};
    hand: Card[];
    // opponent: {id: string, name: string};
    // turn: string;
}

export default function Board({gameId, player, hand}: Props) {
    const [playerHand, playerHandHandler] = useListState(hand);
    const [playerTopThrees, playerTopThreesHandler] = useListState([]);
    const [playerBottomThrees, playerBottomThreesHandler] = useListState([]);
    const [playerFours, playerFoursHandler] = useListState([]);
    const [opponentTopThrees, opponentTopThreesHandler] = useListState([]);
    const [opponentBottomThrees, opponentBottomThreesHandler] = useListState([]);
    const [opponentFours, opponentFoursHandler] = useListState([]);
    const [discardPile, discardPileHandler ] = useListState([]);
    // This will need to change for security reasons
    // The entire list cannot be on the client.
    const [stockPile, stockPileHandler ] = useListState([]);
    // const [turn, setTurn] = useState(turn);
    const playerPrivateChannel = `game.${gameId}.${player.id}`;
    const gamePublicChannel = `game.${gameId}`;

    // set up listeners
    // useEffect(() => {
    //     window.Echo.private(playerPrivateChannel).listen("CardDealt", (e) => {
    //         // const index = playerHand.length;
    //         // playerHandHandler.append(new Card({id: e.card.id, suit: e.card.suit, value: e.card.value, index: index}));
    //         // console.log("player-hand", playerHand);
    //     });
    //     window.Echo.private(playerPrivateChannel).listen("TurnChanged", (e) => {
    //         // setTurn(e.turn);
    //     });
    //     // window.Echo.private(gamePublicChannel).listen("MeldAdded", (e) => {
    //     //     if (e.playerId === player.id) {
    //     //         if (e.meld.length === 3) {
    //     //             playerThreesHandler.add(e.meld);
    //     //         } else {
    //     //             playerFoursHandler.add(e.meld);
    //     //         }
    //     //     } else {
    //     //         if (e.meld.length === 3) {
    //     //             opponentThreesHandler.add(e.meld);
    //     //         } else {
    //     //             opponentFoursHandler.add(e.meld);
    //     //         }
    //     //     }
    //     // });
    //     return () => {
    //         window.Echo.leaveChannel(playerPrivateChannel);
    //         window.Echo.leaveChannel(gamePublicChannel);
    //     };
    // }, []);

    return (
        <>
            <Head title="Board" />
            <div className="relative flex min-h-screen flex-col items-center overflow-hidden bg-dark-blue px-4 pt-5 dark:bg-dark-blue sm:items-center sm:pt-0">
                <GameStats contract={"2 Threes 1 Fours"} turn={'Nina'} />
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
                            cards={opponentTopThrees}
                        />
                        <Meld
                            droppableId={"opponentFour"}
                            cards={opponentFours}
                            className="col-span-2 col-start-3"
                        />
                        <Meld
                            droppableId={"opponentBottomThree"}
                            cards={opponentBottomThrees}
                        />

                        <div className="col-start-2 row-start-3 grid">
                            {stockPile.map((number, index) => (
                                <div className="col-start-1 row-start-1">
                                    <Card
                                        index={number}
                                        key={Math.floor(number * Math.PI)}
                                        value={"joker"}
                                        suit={"hearts"}
                                        faceDown={true}
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
                                    {discardPile
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
                            cards={playerTopThrees}
                            className={"row-start-4"}
                        />

                        <Meld
                            droppableId={"playerBottomThree"}
                            cards={playerBottomThrees}
                            className={"row-start-5"}
                        />

                        <Meld
                            droppableId={"playerFour"}
                            cards={playerFours}
                            className={"col-span-2 col-start-3 row-start-5"}
                        />
                    </div>
                    <div className="mt-[30px]">
                        <ActionBar />
                        <Meld
                            droppableId={"playerHand"}
                            className="mt-5 flex items-center justify-center p-4"
                            cards={playerHand}
                        />
                    </div>
                </DragDropContext>
            </div>
        </>
    );
}