import React from "react";
import { Box, createStyles, Text } from "@mantine/core";

interface Props {
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
    faceDown?: boolean;
}

const useStyles = createStyles((theme) => ({
    card: {
        ...theme.fn.focusStyles(),
        width: "44px",
        height: "62px",
        borderRadius: "5px",
        background: "#CBD6E1",
        border: "1px solid #000000",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        color: "#4D2828",
    },
    jokerLavender: {
        background:
            "radial-gradient(50% 50% at 50% 50%, #D3A9F4 0%, #32373C 100%)",
    },
    jokerTeal: {
        background:
            "radial-gradient(50% 50% at 50% 50%, #41BEC5 0%, #32373C 100%)",
    },
    faceDown: {
        background:
            "radial-gradient(50% 50% at 50% 50%, #1E1E1E 0%, #32373C 100%)",
    },
}));
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
export default function MantineCard({ suit, value, faceDown = false }: Props) {
    const { classes, cx } = useStyles();
    return faceDown ? (
        <Box className={cx(classes.card, classes.faceDown)}></Box>
    ) : (
        <Box
            className={cx(classes.card, {
                [classes.jokerLavender]: value == "joker",
            })}>
            {value != "joker" && <Text>{VALUES[value]}</Text>}
            {value == "joker" ? (
                <Text>{JOKER["suit"]}</Text>
            ) : (
                <Text>{SUITS[suit]}</Text>
            )}
        </Box>
    );
}
