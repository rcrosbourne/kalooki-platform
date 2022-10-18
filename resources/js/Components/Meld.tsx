import React, { ReactNode } from "react";
import { Droppable } from "react-beautiful-dnd";
interface Props {
    droppableId: string;
    cards: ReactNode[];
    className?: string;

}
export default function Meld({ droppableId, cards, className}: Props) {
    return (
        <Droppable droppableId={droppableId} direction="horizontal">
            {(provided) => (
                <div
                    {...provided.droppableProps}
                    ref={provided.innerRef}
                    className={"flex -space-x-6 " + className}>
                    {cards.map((card, index) => (
                        <div key={index} className="flex-1">
                            {card}
                        </div>
                    ))}
                    {provided.placeholder}
                </div>
            )}
        </Droppable>
    );
}
