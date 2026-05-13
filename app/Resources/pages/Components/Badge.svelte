<script lang="ts">
    import {cm, tv} from '@utils';
    import type {Snippet} from "svelte";

    const badge = tv({
        base: "border-2 border-ui-input-border px-2 py-1 text-sm/none font-semibold text-white shadow-[2px_2px_0_0] shadow-ui-input-border",
        variants: {
            variant: {
                info: "bg-ui-info",
                success: "bg-ui-success",
                error: "bg-ui-error",
            },
        },
        defaultVariants: {
            variant: "info",
        }
    });

    interface Props {
        variant?: 'info' | 'success' | 'error';
        content?: string;
        class?: string;
        children?: Snippet;
    }

    let {
        variant = 'info',
        content,
        class: className = '',
        children,
    }: Props = $props();
</script>

<span
    class={cm(badge({ variant }), className)}
>
    {content}
    {@render children?.()}
</span>