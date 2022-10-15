import React, {useEffect, useState} from 'react';
import {Link, Head} from '@inertiajs/inertia-react';
import {DragDropContext, Droppable, Draggable} from 'react-beautiful-dnd';
import {useListState} from '@mantine/hooks';
import {createStyles, Text} from "@mantine/core";

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
    useEffect(() => {
        window.Echo.channel('list-updated')
            .listen('ListUpdate', (e) => {
                //update the states
                let {state, state2, source, destination, id} = e.data;
                if (id !== window.Echo.socketId()) {
                    handlers2.setState(state2);
                    handlers.setState(state);
                    if (destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list') {
                        let card = state[source.index];
                        handlers.remove(source.index);
                        handlers2.insert(destination.index, card);
                    }
                    if (destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list-2') {
                        let card = state2[source.index];
                        handlers2.remove(source.index);
                        handlers.insert(destination.index, card);
                    }
                    // If moving within list 1
                    if (destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list') {
                        handlers.reorder({from: source.index, to: destination.index});

                    }
                    // If moving within list 2
                    if (destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list-2') {
                        handlers2.reorder({from: source.index, to: destination.index});
                    }

                }
            });
        return () => {
            window.Echo.leaveChannel(`list-updated`);
        }
    }, []);
    const items = state.map((item, index) => {
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
    });
    const items2 = state2.map((item, index) => {
        if (item &&  item.value && item.suit) {
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
    });
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
                    onDragEnd={({destination, source}) => {
                        // console.log(destination, source);
                        // If moving from list 1 to list 2
                        if (destination && destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list') {
                            const card = state[source.index];
                            handlers.remove(source.index);
                            handlers2.insert(destination.index, card);
                            //send to server
                            // console.log(destination, source);
                            window.axios.post('/api/insert', {
                                source,
                                destination,
                                state,
                                state2,
                                id: window.Echo.socketId(),
                            });
                            return;
                        }
                        // If moving from list 2 to list 1
                        if (destination && destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list-2') {
                            const card = state2[source.index];
                            handlers2.remove(source.index);
                            handlers.insert(destination.index, card);
                            window.axios.post('/api/insert', {
                                source,
                                destination,
                                state,
                                state2,
                                id: window.Echo.socketId(),
                            });
                            return;
                        }
                        // If moving within list 1
                        if (destination && destination.droppableId === 'dnd-list' && source.droppableId === 'dnd-list') {
                            handlers.reorder({from: source.index, to: destination.index});
                            window.axios.post('/api/insert', {
                                source,
                                destination,
                                state,
                                state2,
                                id: window.Echo.socketId(),
                            });
                            return;
                        }
                        // If moving within list 2
                        if (destination && destination.droppableId === 'dnd-list-2' && source.droppableId === 'dnd-list-2') {
                            handlers2.reorder({from: source.index, to: destination.index});
                            window.axios.post('/api/insert', {
                                source,
                                destination,
                                state,
                                state2,
                                id: window.Echo.socketId(),
                            });
                        }
                    }}
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
