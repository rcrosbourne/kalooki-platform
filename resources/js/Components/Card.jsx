import React from 'react';
import {createStyles} from "@mantine/core";

const useStyles = createStyles((theme) => ({
    card: {
        ...theme.fn.focusStyles(),
        boxSizing: "border-box",
        background: "#CBD6E1",
        border: "1px solid #000000",
        borderRadius: "5px",
        width: "43.7px",
        height: "61.23px",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
    },
    faceUp: {
        fontStyle: "normal",
        fontWeight: "700",
        fontSize: "16px",
        lineHeight: "20px",
        color: "#4D2828",
    },
    faceDown: {
        background: "radial-gradient(50% 50% at 50% 50%, #1E1E1E 0%, #32373C 100%)",
        border: "1px solid #000000",
        borderRadius: "5px",
        width: "43.7px",
        height: "61.23px",
    },
    joker: {
        boxSizing: "border-box",
        borderRadius: "5px",
        width: "43.7px",
        height: "61.23px",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        background: "radial-gradient(50% 50% at 50% 50%, #D3A9F4 0%, #32373C 100%)",
        border: "1px solid #000000"
    }

}));
const SUITS = {"spades": '♠', "clubs": '♣', "hearts": '♥', "diamond": '♦'};
const VALUES = {"ace": 'A', "jack": 'J', "queen": 'Q', "king": 'K', "10": 10, "9": 9, "8": 8, "7": 7, "6": 6, "5": 5, "4": 4, "3": 3, "2": 2};
const JOKER = {"value": "joker", "suit": "👻️"};
export default function Card({suit, value, faceUp}) {
    const {classes, cx} = useStyles();
    if (!faceUp) {
        return (
            <div className={cx(classes.faceDown)}></div>
        );
    }
    if (value === "joker") {
        return (<div className={cx(classes.joker)}>
            <span>{JOKER.suit}</span>
        </div>);
    }
    return (
        <div className={cx(classes.card)}>
            <div className={cx(classes.faceUp)}>
                <span>{VALUES[value]}</span><span>{SUITS[suit]}</span>
            </div>
        </div>
    );
}
