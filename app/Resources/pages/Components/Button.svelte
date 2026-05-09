<script lang="ts">
    import { Button } from "bits-ui";
    import { inertia } from "@inertiajs/svelte";
    import { cm, tv } from "@utils";
    import type { Snippet } from "svelte";
    import type { Uri } from '@route';

    const button = tv({
        base: "inline-block border-2 bg-white px-3 py-1.5 font-semibold focus:outline-0 rounded-xs",
        variants: {
            variant: {
                primary: "border-ui-button text-ui-text shadow-[4px_4px_0_0] hover:bg-ui-focus focus:ring-2 focus:ring-ui-focus",
                secondary: "border-ui-button text-ui-text hover:bg-ui-focus focus:ring-2 focus:ring-ui-focus",
                danger: "border-ui-button-danger text-ui-button-danger-shade shadow-[4px_4px_0_0] shadow-ui-button-danger hover:bg-ui-focus focus:ring-2 focus:ring-ui-focus",
            },
            disabled: {
                true: "opacity-80 cursor-not-allowed pointer-events-none",
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
            <span class="block w-full h-3 my-1.5 rounded-full border border-ui-button-shade bg-ui-button-shade animate-[drain_1.5s_linear_infinite]"></span>
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
            <span class="block w-full h-3 my-1.5 rounded-full border border-ui-button-shade bg-ui-button-shade animate-[drain_1.5s_linear_infinite]"></span>
        {:else}
            {@render children?.()}
        {/if}
    </Button.Root>
{/if}