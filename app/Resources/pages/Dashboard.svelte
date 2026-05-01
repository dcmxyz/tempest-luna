<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from '@layouts'
    import Navbar from '@components/Navbar.svelte'

    interface Props {
        [key: string]: any
    }

    let { ...rest }: Props = $props()

    const auth = $derived(page.props.auth);
</script>

<App title="Dashboard">
    {#snippet header()}
        <Navbar />
    {/snippet}

    <div class="flex flex-col gap-8 py-12">
        <div class="grid grid-cols-3 gap-8">
            {#each [1, 2, 1, 1, 1, 2, 3, 2, 1] as cols}
                <div class="col-span-{cols} border border-ui-surface-border bg-ui-surface-background p-6">
                    <div class="h-24 bg-ui-surface-backgrounds"></div>
                </div>
            {/each}
        </div>
    </div>
</App>