import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export { tv } from "tailwind-variants";

export function cm(...inputs: ClassValue[]): string {
    return twMerge(clsx(...inputs));
}

export function preventDefault(fn: (event: Event) => void) {
    return function (this: unknown, event: Event): void {
        event.preventDefault();
        fn.call(this, event);
    };
}

export function cast<T>(value: unknown): T {
    return value as T;
}

export const normalizeText = (text: string): string =>
    text?.toLowerCase().replace(/\s/g, "") || "";
