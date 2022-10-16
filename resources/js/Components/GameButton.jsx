import React from 'react';

export default function GameButton({ type = 'submit', className = '', processing, children, onClick, }) {
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
