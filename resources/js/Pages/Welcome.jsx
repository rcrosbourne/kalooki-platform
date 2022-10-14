import React from 'react';
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
        value: '',
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
        marginBottom: theme.spacing.sm,
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
export default function Welcome(props) {
    const {classes, cx} = useStyles();
    const [state, handlers] = useListState(data);
    const items = state.map((item, index) => (
        <Draggable key={item.value + item.suit} index={index} draggableId={item.value + item.suit}>
            {(provided, snapshot) => (
                <div
                    className={cx(classes.item, {[classes.itemDragging]: snapshot.isDragging}) }
                    {...provided.draggableProps}
                    {...provided.dragHandleProps}
                    ref={provided.innerRef}
                >
                    <Text className={item.color === 'red' ? 'text-red-700' : 'text-black'}>{item.value}{item.suit}</Text>
                </div>
            )}
        </Draggable>
    ));
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
                    onDragEnd={({destination, source}) =>
                        handlers.reorder({from: source.index, to: destination?.index || 0})
                    }
                >
                    <Droppable droppableId="dnd-list" direction="horizontal" >
                        {(provided) => (
                            <div {...provided.droppableProps} ref={provided.innerRef} className="flex -space-x-6 md:-space-x-2 h-4">
                                {items}
                                {provided.placeholder}
                            </div>
                        )}
                    </Droppable>
                </DragDropContext>
            </div>
        </>
    );
}
