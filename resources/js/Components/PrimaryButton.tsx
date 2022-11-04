import React, { MouseEventHandler } from "react";
interface Props {
    type?: 'button' | 'submit' | 'reset' | undefined;
    className?: string;
    children: React.ReactNode;
    processing?: boolean;
    onClick?: MouseEventHandler<HTMLButtonElement>,
}
export default function PrimaryButton({ type = 'submit', className = '', processing, onClick, children }: Props) {
    return (
        <button
            type={type}
            className={
                `inline-flex items-center px-4 py-2 bg-light-green border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-emerald-900 transition ease-in-out duration-150 ${
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
