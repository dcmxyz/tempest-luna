<script lang="ts">
    import { Button } from "bits-ui";
    import { inertia } from "@inertiajs/svelte";
    import { cm, tv } from "@utils";
    import type { Snippet } from "svelte";
    import type { Uri } from '@route';

    const button = tv({
        base: "inline-block rounded-xs px-3 py-2.5 text-lg font-medium transition leading-none cursor-pointer border border-transparent focus:ring-2 focus:ring-offset-2 focus:outline-5 focus:outline-offset-2 select-none focus:bg-ui-focus focus:text-ui-focus-text",
        variants: {
            variant: {
                primary: "text-white bg-ui-button hover:bg-ui-button-shade focus:ring-ui-button focus:outline-ui-button/40 active:ring-ui-button active:outline-ui-button/40 active:shadow-none",
                secondary: "text-ui-button border bg-white border-ui-button hover:bg-ui-button-shade/20 focus:ring-ui-button focus:outline-ui-button/40 active:ring-ui-button active:outline-ui-button/40 active:shadow-none",
                danger: "text-white bg-ui-button-danger hover:bg-ui-button-danger-shade focus:ring-ui-danger focus:outline-ui-danger/40 active:ring-ui-danger active:outline-ui-danger/40 active:shadow-none",
            },
            disabled: {
                true: "opacity-80 bg-white border-ui-button cursor-not-allowed pointer-events-none",
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
        loading?: boolean;
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
        loading = false,
        inertiaProps = { method: 'get' },
        class: className = '',
        children,
        ...rest
    }: Props = $props();


    const resolvedHref = $derived(href ? (href.startsWith('/') ? href : `/${href}`) : undefined);
    const isDisabled = $derived(disabled || loading);
</script>

{#if resolvedHref}
    <a
        role="button"
        draggable="false"
        use:inertia={{ href: resolvedHref, ...inertiaProps }}
        class={cm(button({ variant, disabled: isDisabled }), className)}
        aria-disabled={isDisabled}
        {...rest}
    >
        {#if loading}
            <span class="block w-full h-2.5 my-1 rounded-full border border-ui-button-shade bg-ui-button-shade animate-[drain_1.5s_linear_infinite]"></span>
        {:else}
            {@render children?.()}
        {/if}
    </a>
{:else}
    <Button.Root
        {type}
        disabled={isDisabled}
        class={cm(button({ variant, disabled: isDisabled }), className)}
        aria-disabled={isDisabled}
        {...rest}
    >
        {#if loading}
            <span class="block w-full h-2.5 my-1 rounded-full border border-ui-button-shade bg-ui-button-shade animate-[drain_1.5s_linear_infinite]"></span>
        {:else}
            {@render children?.()}
        {/if}
    </Button.Root>
{/if}