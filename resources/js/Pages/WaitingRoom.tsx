import React, { useEffect, useState } from "react";
import PrimaryButton from "../Components/PrimaryButton";
import { Inertia } from "@inertiajs/inertia";
import {usePage} from "@inertiajs/inertia-react";
import route from "../../../vendor/tightenco/ziggy/dist/index.m";

interface Player {
  name: string;
  id: number;
}

interface Props {
  gameId: string;
  code: string;
  inviteLink: string;
  players: Player[]; // array of player ids
  isCreator: boolean;
}

export default function WaitingRoom(props: Props) {
  const [players, setPlayers] = useState(props.players);
  const {auth } = usePage().props;
  useEffect(() => {
    window.Echo.private(`game-${props.gameId}`).listen("PlayerJoined", (e) => {
      setPlayers((players) => [...players, e.player]);
    });
    window.Echo.private(`started.${props.gameId}.${auth.user.id}`).listen("GameStarted", (e) => {
      Inertia.visit(route("game.play", { game: props.gameId }));
    });
    return () => {
      window.Echo.leaveChannel(`game-${props.gameId}`);
      window.Echo.leaveChannel(`started.${props.gameId}.${auth.user.id}`);
    };
  });
  const startGame = (e) => {
    // send a request to start gamee with the game id
    Inertia.post(route("game.start", props.gameId));
  }
  return (
    <div>
      <h1>Waiting Room</h1>
      <h2>Game ID: {props.gameId}</h2>
      <h2>Code: {props.code}</h2>
      <h2>Invite Link: {props.inviteLink}</h2>
      <ul>
        {players.map((player) => (
          <li key={player.id}>{player.name}</li>
        ))}
      </ul>
      {props.isCreator && <PrimaryButton type="button" processing={players.length < 2} onClick={startGame}>Start game</PrimaryButton>}
    </div>
  );
}