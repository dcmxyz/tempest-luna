<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from '@layouts'
    import { Navbar } from '@components'

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

    <div class="flex flex-col gap-6 pt-8 flex-1">
        <div class="grid grid-cols-3 gap-8 flex-1">
            {#each [1, 2, 1, 1, 1, 2, 1, 1, 2] as cols}
                <div class="col-span-{cols} border border-ui-surface-border bg-ui-surface-background opacity-50 p-6">
                    <div class="bg-ui-surface-backgrounds"></div>
                </div>
            {/each}
        </div>
    </div>
</App>