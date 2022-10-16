import React from "react";
import { Droppable } from "react-beautiful-dnd";

export default function Meld({ droppableId, cards, className}) {
    return (
        <Droppable droppableId={droppableId} direction="horizontal">
            {(provided) => (
                <div
                    {...provided.droppableProps}
                    ref={provided.innerRef}
                    className={"flex -space-x-4 " + className}>
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
