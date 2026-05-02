<script lang="ts">
    import { cm, tv } from "@utils";
    import type {Snippet} from "svelte";

    const alert = tv({
        base: "border-l-6 p-2",
        variants: {
            variant: {
                info: "border-ui-info bg-ui-info/10",
                success: "border-ui-success bg-ui-success/10",
                warning: "border-ui-warning bg-ui-warning/10",
                danger: "border-ui-danger bg-ui-danger/10",
            },
        },
        defaultVariants: {
            variant: "info",
        }
    });

    interface Props {
        variant?: 'info' | 'success' | 'warning' | 'danger';
        title?: string;
        message?: string;
        class?: string;
        children?: Snippet;
    }

    let {
        variant,
        title,
        message,
        class: className = '',
        children,
    }: Props = $props();
</script>

<div class={cm(alert({ variant }), className)}>
    {#if title}
        <h6 class="font-bold">{ title }</h6>
    {/if}
    {#if message }
        <p>{ message }</p>
    {/if}
    {@render children?.()}
</div>