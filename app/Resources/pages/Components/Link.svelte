<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import { cm, tv } from "@utils";
    import type { Snippet } from "svelte";
    import type { Uri } from '@route';

    const link = tv({
        base: "text-lg leading-none py-0.5 transition-colors focus:outline-none focus:bg-ui-focus focus:text-ui-focus-text underline-offset-2 decoration-1 hover:underline-offset-4 hover:decoration-2 focus:decoration-3",
        variants: {
            variant: {
                link: "underline text-ui-link hover:text-ui-link-hover visited:text-ui-link-visited active:text-ui-link-active",
                "no-visited": "underline text-ui-link hover:text-ui-link-hover active:text-ui-link-active",
                "no-underline": "text-ui-link [text-decoration:none] hover:text-ui-link-hover visited:text-ui-link-visited active:text-ui-link-active",
            }
        },
        defaultVariants: {
            variant: "link"
        }
    });

    type Href = Uri | (string & {});

    interface Props {
        href?: Href;
        variant?: "link" | "no-visited" | "no-underline";
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
    class={cm(link({ variant }), className)}
    {...rest}
>
    {@render children?.()}
</a>