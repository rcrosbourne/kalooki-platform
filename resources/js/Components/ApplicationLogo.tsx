import React from "react";

interface Props {
  className: string;
}

export default function ApplicationLogo({ className }: Props) {
  return (
    <img
      className={className}
      src="/img/IMG_3085.PNG"
      alt=""
    />
  );
}