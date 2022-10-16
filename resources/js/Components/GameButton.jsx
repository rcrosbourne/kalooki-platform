import React from 'react';

export default function GameButton({ type = 'submit', className = '', processing, children, onClick, }) {
    return (
        <button
            type={type}
            className={
                `w-full rounded-[3px] bg-[#12B886] py-[8px] px-[10px] font-bold uppercase text-[#CBD6E1] ${
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
