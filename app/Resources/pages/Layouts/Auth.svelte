<script lang="ts">
    import type { Snippet } from "svelte";
    import { Base } from "@layouts";

    interface Props {
        title?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let { title = undefined, children, ...rest }: Props = $props();
</script>

<Base {title}>
    {@render children?.()}
</Base>