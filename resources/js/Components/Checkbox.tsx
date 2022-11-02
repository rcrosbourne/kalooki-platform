import React from 'react';

interface Props {
    name: string;
    value: string;
    handleChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

export default function Checkbox({ name, value, handleChange }: Props) {
    return (
        <input
            type="checkbox"
            name={name}
            value={value}
            className="rounded border-gray-300 text-light-green shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
            onChange={(e) => handleChange(e)}
        />
    );
}
