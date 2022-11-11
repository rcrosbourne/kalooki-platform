import { Link, Head, usePage } from "@inertiajs/inertia-react";
import React, { useEffect, useState } from "react";
import Card from "@/Components/Card";
import { DragDropContext, Droppable } from "react-beautiful-dnd";
import { useListState, useId, useDebouncedState } from "@mantine/hooks";
import GameStats from "@/Components/GameStats";
import Meld from "@/Components/Meld";
import ActionBar from "@/Components/ActionBar";
import axios from "axios";

interface Props {
  gameId: string;
  player: { id: string, name: string };
  hand: Card[];
  opponent: string;
  turn: string;
  isTurn: boolean;
  stock: Card[];
  discard: Card[];
  playerTopThrees: Card[];
  playerBottomThrees: Card[];
  playerFours: Card[];
  opponentTopThrees: Card[];
  opponentBottomThrees: Card[];
  opponentFours: Card[];
}

export default function Board({ gameId, player, hand, opponent, turn, isTurn, stock, discard,
                                playerTopThrees:playerT3, playerBottomThrees:playerB3, playerFours:player4s, opponentTopThrees:opponentT3,
                                opponentBottomThrees:opponentB3, opponentFours:opponent4s }: Props) {
  const [playerHand, playerHandHandler] = useListState(hand);
  const [playerActions, setPlayerActions] = useState([]);
  const [myTurn, setMyTurn] = useState(isTurn);
  const [whoTurn, setWhoTurn] = useState(turn);
  const [playerTopThrees, playerTopThreesHandler] = useListState(playerT3);
  const [playerBottomThrees, playerBottomThreesHandler] = useListState(playerB3);
  const [playerFours, playerFoursHandler] = useListState(player4s);
  const [opponentTopThrees, opponentTopThreesHandler] = useListState(opponentT3);
  const [opponentBottomThrees, opponentBottomThreesHandler] = useListState(opponentB3);
  const [opponentFours, opponentFoursHandler] = useListState(opponent4s);
  const [discardPile, discardPileHandler] = useListState(discard);
  // This will need to change for security reasons
  // The entire list cannot be on the client.
  const [stockPile, stockPileHandler] = useListState(stock);
  // const [turn, setTurn] = useState(turn);
  const playerPrivateChannel = `game.${gameId}.${player.id}`;
  const gamePublicChannel = `game.${gameId}`;

  useEffect(() => {
    if (myTurn) {
      axios.get(`/kalooki/${gameId}/available-moves`).then(({ data }) => {
        setPlayerActions(data);
      });
    }
  }, [myTurn]);
  // set up listeners
  useEffect(() => {
    window.Echo.private(playerPrivateChannel).listen("PlayerTurnNotification", (e) => {
      setMyTurn(true);
      setWhoTurn("Yours");
    });
    window.Echo.channel(gamePublicChannel).listen("BoardStateUpdated", (e) => {
      discardPileHandler.setState(e.boardState.discard);
      stockPileHandler.setState(e.boardState.stock);
      console.log(e.boardState);
      if(e.boardState.topThrees) {
        if(e.boardState.playerId === player.id) {
          playerTopThreesHandler.setState(e.boardState.topThrees);
        } else {
          opponentTopThreesHandler.setState(e.boardState.topThrees);
        }
      }
      if(e.boardState.bottomThrees) {
        if(e.boardState.playerId === player.id) {
          playerBottomThreesHandler.setState(e.boardState.bottomThrees);
        } else {
          opponentBottomThreesHandler.setState(e.boardState.bottomThrees);
        }
      }
      if(e.boardState.fours) {
        if(e.boardState.playerId === player.id) {
          playerFoursHandler.setState(e.boardState.fours);
        } else {
          opponentFoursHandler.setState(e.boardState.fours);
        }
      }
      if(e.boardState.opponentFours) {
       opponentFoursHandler.setState(e.boardState.opponentFours);
      }
      if(e.boardState.opponentTopThrees) {
       opponentTopThreesHandler.setState(e.boardState.opponentTopThrees);
      }
      if(e.boardState.opponentBottomThrees) {
        opponentBottomThreesHandler.setState(e.boardState.opponentBottomThrees);
      }
    });

    return () => {
      window.Echo.leaveChannel(playerPrivateChannel);
      window.Echo.leaveChannel(gamePublicChannel);
    };
  }, []);

  const onStockPileClick = () => {
    if (myTurn && playerActions.includes("requestCardFromStockPile")) {
      // request card from stock pile
      axios.post(`/kalooki/${gameId}/request-card-from-stock-pile`).then(({ data }) => {
        playerHandHandler.setState(data.hand);
        stockPileHandler.setState(data.stock);
        setPlayerActions(data.availableActions);
      });
    }
  };

  const onDiscardPileClick = () => {
    if (myTurn && playerActions.includes("requestCardFromDiscardPile")) {
      // request card from stock pile
      axios.post(`/kalooki/${gameId}/request-card-from-discard-pile`).then(({ data }) => {
        playerHandHandler.setState(data.hand);
        discardPileHandler.setState(data.discard);
        setPlayerActions(data.availableActions);
      });
    }
  };


  const onTurnEnd = () => {
    if (myTurn && playerActions.includes("endTurn")) {
      axios.post(`/kalooki/${gameId}/end-turn`).then(({ data }) => {
        setPlayerActions(data.availableActions);
        setMyTurn(data.isTurn);
        setWhoTurn(data.turn);
      });
    }
  };
  const onLayDownCards = () => {
    if (myTurn && playerActions.includes("layDownCards")) {
      axios.post(`/kalooki/${gameId}/lay-cards`).then(({ data }) => {
        playerHandHandler.setState(data.hand);
        setPlayerActions(data.availableActions);
      });
    }
  }
  const onTackOnCards = () => {
    if (myTurn && playerActions.includes("tackOnCards")) {
      axios.post(`/kalooki/${gameId}/tack-on-cards`).then(({ data }) => {
        playerHandHandler.setState(data.hand);
        setPlayerActions(data.availableActions);
      });
    }
  }
  const onDragEnd = (result) => {
    const { destination, source, draggableId } = result;
    if (destination.droppableId === source.droppableId) {
      // reorder
      if (destination.droppableId === "playerHand") {
        playerHandHandler.reorder({
          from: source.index,
          to: destination.index
        });
        // persist the order on the server
        axios.post(`/kalooki/${gameId}/reorder-hand`, {
          from: source.index,
          to: destination.index
        }).then(({ data }) => {
          playerHandHandler.setState(data.hand);
          // setPlayerActions(data.availableActions);
        });
      }
    }
    if (destination.droppableId === "discardPile") {
      // discard
      axios.post(`/kalooki/${gameId}/discard-card-from-hand`, {
        card: draggableId
      }).then(({ data }) => {
        playerHandHandler.setState(data.hand);
        discardPileHandler.setState(data.discard);
        setPlayerActions(data.availableActions);
      });
    }
  };

  return (
    <>
      <Head title="Board" />
      <div className="relative flex min-h-screen flex-col items-center overflow-hidden bg-dark-blue px-4 pt-5 dark:bg-dark-blue sm:items-center sm:pt-0">
        <GameStats opponent={opponent} turn={whoTurn} />
        <DragDropContext onDragEnd={onDragEnd}>
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

            <div className="col-start-2 row-start-3 grid" onClick={onStockPileClick}>
              {stockPile.map((card, index) => (
                <div className="col-start-1 row-start-1" key={`${card.id}-${index}`}>
                  <Card
                    {...card}
                    faceDown={true}
                    index={index}
                  />
                </div>
              ))}
            </div>
            <Droppable droppableId="discardPile" direction="none">
              {(provided) => (
                <div
                  {...provided.droppableProps}
                  ref={provided.innerRef}
                  className="col-start-3 row-start-3 grid"
                  onClick={onDiscardPileClick}>
                  {discardPile
                    .map((card, index) => (
                      <div
                        key={index}
                        className="col-start-1 row-start-1 flex-1">
                        <Card {...card} index={index} />
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
            <ActionBar disableActions={!myTurn} availableActions={playerActions}
                       onTurnEnd={onTurnEnd}
                       onLayDownCards={onLayDownCards}
                       onTackOnCards={onTackOnCards}/>
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