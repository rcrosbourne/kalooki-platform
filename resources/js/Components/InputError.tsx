import React from "react";

interface Props {
    message: string;
    className?: string;
}
export default function InputError({ message, className = '' }: Props) {
    return message ? <p className={'text-sm text-light-pink font-bold ' + className}>{message}</p> : null;
}
