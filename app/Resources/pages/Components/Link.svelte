<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import { cm, tv } from "@utils";
    import type { Snippet } from "svelte";
    import type { Uri } from '@route';

    const link = tv({
        base: "text-sm transition-colors",
        variants: {
            variant: {
                link: "text-stone-400 hover:text-stone-900",
                button: "px-4 py-1.5 font-bold bg-stone-900 text-white hover:bg-stone-700",
            }
        },
        defaultVariants: {
            variant: "link"
        }
    });

    type Href = Uri | (string & {});

    interface Props {
        href?: Href;
        variant?: "button" | "link";
        inertiaProps?: Record<string, any>;
        class?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let {
        href = undefined,
        variant = "link",
        inertiaProps = { method: 'get' },
        class: className = '',
        children,
        ...rest
    }: Props = $props();

    const resolvedHref = $derived(href?.startsWith('/') ? href : `/${href}`);
</script>

<a
    use:inertia={{ href: resolvedHref, ...inertiaProps }}
    type="button"
    class={cm(link({ variant }), className)}
    {...rest}
>
    {@render children?.()}
</a>