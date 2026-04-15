<script lang="ts">
    import { Button } from "bits-ui";
    import { inertia } from "@inertiajs/svelte";
    import { cm, tv } from "@utils";
    import type { Snippet } from "svelte";
    import type { Uri } from '@route';

    const button = tv({
        base: "inline-block px-3 py-2.5 text-lg leading-none cursor-pointer transition-colors focus:outline-none focus:bg-ui-focus focus:text-ui-focus-text focus:shadow-ui-button-shade",
        variants: {
            variant: {
                primary: "text-white bg-ui-button shadow-[0_2px_0_var(--color-ui-button-shade)] hover:bg-ui-button-shade active:shadow-none active:translate-y-[2px]",
                secondary: "text-ui-text bg-ui-button-secondary shadow-[0_2px_0_var(--color-ui-button-secondary-shade)] hover:bg-ui-button-secondary-shade/50 active:shadow-none active:translate-y-[2px]",
                danger: "text-white bg-ui-button-danger shadow-[0_2px_0_var(--color-ui-button-danger-shade)] hover:bg-ui-button-danger-shade active:shadow-none active:translate-y-[2px]",
            },
            disabled: {
                true: "opacity-50 cursor-not-allowed pointer-events-none",
                false: "",
            }
        },
        defaultVariants: {
            variant: "primary",
            disabled: false,
        }
    });

    type Href = Uri | (string & {});

    interface Props {
        href?: Href;
        type?: "button" | "submit" | "reset";
        variant?: "primary" | "secondary" | "danger";
        disabled?: boolean;
        inertiaProps?: Record<string, any>;
        class?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let {
        href = undefined,
        type = "button",
        variant = "primary",
        disabled = false,
        inertiaProps = { method: 'get' },
        class: className = '',
        children,
        ...rest
    }: Props = $props();

    const resolvedHref = $derived(href ? (href.startsWith('/') ? href : `/${href}`) : undefined);
</script>

{#if resolvedHref}
    <a
        role="button"
        draggable="false"
        use:inertia={{ href: resolvedHref, ...inertiaProps }}
        class={cm(button({ variant, disabled }), className)}
        aria-disabled={disabled}
        {...rest}
    >
        {@render children?.()}
    </a>
{:else}
    <Button.Root
        {type}
        {disabled}
        class={cm(button({ variant, disabled }), className)}
        aria-disabled={disabled}
        {...rest}
    >
        {@render children?.()}
    </Button.Root>
{/if}