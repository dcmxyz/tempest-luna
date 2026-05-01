<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    interface Props {
        title?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let { title = undefined, children, ...rest }: Props = $props();

    const appName = page.props.app?.name || "";
    const pageTitle = $derived([title, appName].filter(Boolean).join(' – '));
</script>

<svelte:head>
    <title>{pageTitle}</title>
</svelte:head>

<main class="h-[calc(100dvh---spacing(8))] flex flex-col border border-ui-border p-4 overflow-hidden bg-ui-body-background overflow-y-scroll">
    {@render children?.()}
</main>
