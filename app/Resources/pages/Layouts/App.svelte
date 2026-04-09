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
    {@render children?.()}
</Base>