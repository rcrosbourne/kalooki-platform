import React from "react";
import {
    Container,
    createStyles,
    Box,
    Text,
    Grid,
    Button,
} from "@mantine/core";
import MantineCard from "../Components/MantineCard";
import GameButton from "../Components/GameButton";

const useStyles = createStyles((theme) => ({
    container: {
        backgroundColor: "#152938",
        padding: theme.spacing.xs,
        height: "100vh",
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
    },
    gameStats: {
        display: "flex",
        justifyContent: "space-between",
        background: "#32373C",
        borderRadius: "10px",
        width: "100%",
        color: "#D3DCE4",
        fontWeight: 700,
        padding: `${theme.spacing.sm}px ${theme.spacing.xs}px`,
    },
    gameBoard: {
        marginTop: "30px",
        background: "#27577B",
        padding: `${theme.spacing.sm}px ${theme.spacing.xs}px`,
        borderRadius: theme.spacing.xs,
        minHeight: "433px",
        width: "100%",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
    },
    playerActions: {
        marginTop: "30px",
    },
    playerHand: {
        marginTop: "12px",
        width: "100%",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
    },
    stack: {
        display: "flex",
        width: "100%",
        alignItems: "center",
    },
}));

export default function MantineBoard() {
    const { classes } = useStyles();

    return (
        <Container className={classes.container}>
            <Box className={classes.gameStats}>
                <Text>Contract: 2 Threes 1 Four</Text>
                <Text>Turn: Nina</Text>
            </Box>
            <Box className={classes.gameBoard}>
                <Grid columns={4}>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-3"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"hearts"} value={"ace"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                        </Box>
                    </Grid.Col>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-2"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"hearts"} value={"ace"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                            <MantineCard suit={"clubs"} value={"joker"} />
                        </Box>
                    </Grid.Col>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-3"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                            <MantineCard suit={"clubs"} value={"joker"} />
                        </Box>
                    </Grid.Col>
                    <Grid.Col span={3}></Grid.Col>
                    <Grid.Col span={1} offset={1}>
                        <MantineCard
                            suit={"clubs"}
                            value={"joker"}
                            faceDown={true}
                        />
                    </Grid.Col>
                    <Grid.Col span={1}>
                        <MantineCard suit={"clubs"} value={"joker"} />
                    </Grid.Col>
                    <Grid.Col span={1}></Grid.Col>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-3"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"hearts"} value={"ace"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                        </Box>
                    </Grid.Col>
                    <Grid.Col span={3}></Grid.Col>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-3"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"hearts"} value={"ace"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                        </Box>
                    </Grid.Col>
                    <Grid.Col span={2}>
                        <Box className={classes.stack + " -space-x-2"}>
                            <MantineCard suit={"spades"} value={"10"} />
                            <MantineCard suit={"hearts"} value={"ace"} />
                            <MantineCard suit={"clubs"} value={"jack"} />
                            <MantineCard suit={"clubs"} value={"joker"} />
                        </Box>
                    </Grid.Col>
                </Grid>
            </Box>
            <Box className={classes.playerActions}>
                <Grid columns={3}>
                    <Grid.Col span={1}>
                       <GameButton>Draw</GameButton>
                    </Grid.Col>
                    <Grid.Col span={1}>
                        <GameButton>Discard</GameButton>
                    </Grid.Col>
                    <Grid.Col span={1}>
                        <GameButton>Lay</GameButton>
                    </Grid.Col>
                </Grid>
                <Box className={classes.playerHand + " -space-x-4"}>
                    <MantineCard suit={"spades"} value={"10"} />
                    <MantineCard suit={"hearts"} value={"ace"} />
                    <MantineCard suit={"clubs"} value={"jack"} />
                    <MantineCard suit={"diamond"} value={"3"} />
                    <MantineCard suit={"diamond"} value={"joker"} />
                    <MantineCard
                        suit={"diamond"}
                        value={"joker"}
                        faceDown={true}
                    />
                    <MantineCard suit={"spades"} value={"10"} />
                    <MantineCard suit={"hearts"} value={"ace"} />
                    <MantineCard suit={"clubs"} value={"jack"} />
                    <MantineCard suit={"diamond"} value={"3"} />
                    <MantineCard suit={"diamond"} value={"joker"} />
                    <MantineCard
                        suit={"diamond"}
                        value={"joker"}
                        faceDown={true}
                    />
                </Box>
            </Box>
        </Container>
    );
}
