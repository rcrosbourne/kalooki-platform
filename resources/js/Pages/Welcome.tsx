import React, {useEffect, useState} from 'react';
import {Link, Head} from '@inertiajs/inertia-react';
import {DragDropContext, Droppable, Draggable} from 'react-beautiful-dnd';
import {useListState} from '@mantine/hooks';
import {createStyles, Text} from "@mantine/core";
import axios from 'axios';
import route from '../../../vendor/tightenco/ziggy/dist/index.m';

const data = [
    {
        value: 'A',
        suit: '♠',
        color: 'black',
    },
    {
        value: 'A',
        suit: '♣️',
        color: 'black',
    },
    {
        value: 'A',
        suit: '♥️',
        color: 'red',
    },
    {
        value: 'A',
        suit: '♦️',
        color: 'red',
    },
    {
        value: 10,
        suit: '♣️',
        color: 'black',
    },
    {
        value: 10,
        suit: '♠',
        color: 'black',
    },
    {
        value: 10,
        suit: '♥️',
        color: 'red',
    },
    {
        value: 10,
        suit: '♦️',
        color: 'red',
    },
    {
        value: 6,
        suit: '♠',
        color: 'black',
    },
    {
        value: 6,
        suit: '♣️',
        color: 'black',
    },
    {
        value: 6,
        suit: '♥️',
        color: 'red',
    },
    {
        value: 6,
        suit: '♦️',
        color: 'red',
    },
    {
        value: 'J',
        suit: '👻️',
        color: 'red',
    },
];
const useStyles = createStyles((theme) => ({
    item: {
        ...theme.fn.focusStyles(),
        display: 'flex',
        alignItems: 'center',
        borderRadius: theme.radius.md,
        border: `1px solid ${
            theme.colorScheme === 'dark' ? theme.colors.dark[5] : theme.colors.gray[2]
        }`,
        padding: `${theme.spacing.md}px ${theme.spacing.xs}px`,
        backgroundColor: theme.colorScheme === 'dark' ? theme.colors.dark[5] : theme.white,
        // marginBottom: theme.spacing.sm,
        height: 70,
    },


    itemDragging: {
        boxShadow: theme.shadows.sm,
        border: `1px solid ${theme.colors.red[5]}`,
    },

    symbol: {
        fontSize: 30,
        fontWeight: 700,
        width: 60,
    },
}));
let data2 = [];
export default function Welcome(props) {
    const {classes, cx} = useStyles();
    const [state, handlers] = useListState(data);
    const [state2, handlers2] = useListState(data2);

    const renderItems = (item, index) => {
        if (item && item.value && item.suit) {
            return <Draggable key={item.value + item.suit} index={index} draggableId={item.value + item.suit}>
                {(provided, snapshot) => (
                    <div
                        className={cx(classes.item, {[classes.itemDragging]: snapshot.isDragging})}
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                        ref={provided.innerRef}
                    >
                        <Text className={item.color === 'red' ? 'text-red-700' : 'text-black'}>{item.value}{item.suit}</Text>
                    </div>
                )}
            </Draggable>
        }
    }
    const setItemState = (state, state2) => {
        handlers.setState(state);
        handlers2.setState(state2);
    }
    const moveFromList1ToList2 = (source, destination, state, state2) => {
        setItemState(state, state2);
        const card = state[source.index];
        handlers.remove(source.index);
        handlers2.insert(destination.index, card);
    }
    const moveFromList2ToList1 = (source, destination, state, state2) => {
        setItemState(state, state2);
        const card = state2[source.index];
        handlers2.remove(source.index);
        handlers.insert(destination.index, card);
    }
    const reorderList1 = (source, destination, state, state2) => {
        setItemState(state, state2);
        handlers.reorder({from: source.index, to: destination.index});
    }
    const reorderList2 = (source, destination, state, state2) => {
        setItemState(state, state2);
        handlers2.reorder({from: source.index, to: destination.index});
    }
    const movingFromList1ToList2 = (destination, source) => {
        return destination && destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list';
    }
    const updateServer = (source, destination, state, state2) => {
        axios.post('/api/insert', {
            source,
            destination,
            state,
            state2,
            id: window.Echo.socketId(),
        });
    }
    const movingFromList2ToList1 = (destination, source) => {
        return destination && destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list-2';
    }

    const reorderingList1 = (destination, source) => {
        return destination && destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list';
    }

    const reorderingList2 = (destination, source) => {
        return destination && destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list-2';
    }
    const updateBoard = (destination, source,  state, state2) => {
        if (movingFromList1ToList2(destination, source)) {
            moveFromList1ToList2(source, destination, state, state2);
        }
        if (movingFromList2ToList1(destination, source)) {
            moveFromList2ToList1(source, destination, state, state2);
        }
        // If moving within list 1
        if (reorderingList1(destination, source)) {
            reorderList1(source, destination, state, state2);
        }
        // If moving within list 2
        if (reorderingList2(destination, source)) {
            reorderList2(source, destination, state, state2);
        }
    }
    const onBoardUpdated = ({data}) => {
        let {state, state2, source, destination, id} = data;
        if (id !== window.Echo.socketId()) {
            updateBoard(destination, source, state, state2);
        }
    }

    const onDragEnd = ({source, destination}) => {
        if (!destination) {
            return;
        }
        updateBoard(destination, source, state, state2);
        updateServer(source, destination, state, state2);
    }

    const items = state.map(renderItems);
    const items2 = state2.map(renderItems);

    useEffect(() => {
        window.Echo.channel('list-updated')
            .listen('ListUpdate', (e) => onBoardUpdated(e));
        return () => {
            window.Echo.leaveChannel(`list-updated`);
        }
    }, []);


    return (
        <>
            <Head title="Welcome"/>
            <div className="relative flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
                <div className="fixed top-0 right-0 px-6 py-4 sm:block">
                    {props.auth.user ? (
                        <Link href={route('dashboard')} className="text-sm text-gray-700 dark:text-gray-500 underline">
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link href={route('login')} className="text-sm text-gray-700 dark:text-gray-500 underline">
                                Log in
                            </Link>

                            <Link
                                href={route('register')}
                                className="ml-4 text-sm text-gray-700 dark:text-gray-500 underline"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </div>
                <DragDropContext
                    onDragEnd={(result) => onDragEnd(result)}
                >
                    <div className="flex flex-col space-y-24">
                        <Droppable droppableId="dnd-list" direction="horizontal">
                            {(provided) => (
                                <div {...provided.droppableProps} ref={provided.innerRef} className="flex -space-x-6 md:-space-x-2 border border-blue-500 rounded p-2">
                                    {items}
                                    {provided.placeholder}
                                </div>
                            )}
                        </Droppable>
                        <Droppable droppableId="dnd-list-2" direction="horizontal">
                            {(provided) => (
                                <div {...provided.droppableProps} ref={provided.innerRef} className="flex -space-x-6 md:-space-x-2 border border-red-500 mt-24 p-2 rounded">
                                    {items2}
                                    {provided.placeholder}
                                </div>
                            )}
                        </Droppable>

                    </div>
                </DragDropContext>
            </div>
        </>
    );
}
