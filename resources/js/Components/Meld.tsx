import React, { ReactNode } from "react";
import { Droppable } from "react-beautiful-dnd";
interface Props {
    droppableId: string;
    cards: ReactNode[];
    className?: string;

}
export default function Meld({ droppableId, cards, className}: Props) {
    const getMeldSpacing = () => {
        if(cards.length <= 4) {
            return "-space-x-6 "
        } else {
            return "-space-x-8 "
        }
    }
    return (
        <Droppable droppableId={droppableId} direction="horizontal">
            {(provided) => (
                <div
                    {...provided.droppableProps}
                    ref={provided.innerRef}
                    className={"flex " + getMeldSpacing() + className}>
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
