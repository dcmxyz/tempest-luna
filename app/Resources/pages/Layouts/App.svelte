<script lang="ts">
    import type { Snippet } from "svelte";
    import { Base } from "@layouts";

    interface Props {
        title?: string;
        header?: Snippet;
        children?: Snippet;
        [key: string]: any;
    }

    let { title = undefined, header, children, ...rest }: Props = $props();
</script>

<Base {title}>
    {@render header?.()}
    <main class="overflow-y-scroll w-full h-full flex px-8 py-4">
        {@render children?.()}
    </main>
</Base>