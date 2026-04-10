<script lang="ts">
    import type { Snippet } from "svelte";
    import { Base } from "@layouts";
    import { uri } from "@route";
    import { Link } from "@components";
    import {page} from "@inertiajs/svelte";

    interface Props {
        title?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let {
        title = undefined,
        children,
        ...rest
    }: Props = $props();
</script>

<Base {title}>
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-4">
            <header class="pl-12 pr-6 py-3 border border-ui-border bg-ui-surface-background">
                <nav class="flex items-center justify-between gap-x-6">
                    <Link href={ uri('/') } variant="no-visited">&leftarrow; Home</Link>
                </nav>
            </header>

            <div class="flex flex-col sm:flex-row border border-ui-border">
                <div class="flex-1 p-8 sm:p-12 flex flex-col justify-between gap-10 border-b sm:border-b-0 sm:border-r border-ui-border">
                    {@render children?.()}
                </div>
            </div>
        </div>
    </div>
</Base>