import React, { MouseEventHandler } from "react";
interface Props {
    type?: 'button' | 'submit' | 'reset' | undefined;
    className?: string;
    children: React.ReactNode;
    processing?: boolean;
    onClick?: MouseEventHandler<HTMLButtonElement>;
}

export default function GameButton({ type = 'submit', className = '', processing, children, onClick }:Props) {
    return (
        <button
            type={type}
            className={
                `w-full rounded bg-light-green py-3 px-2 font-bold  uppercase text-gray-50 ${
                    processing && 'opacity-25'
                } ` + className
            }
            disabled={processing}
            onClick={onClick}
        >
            {children}
        </button>
    );
}
