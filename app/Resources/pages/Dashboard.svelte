<script lang="ts">
    import { App } from '@layouts'
    import { Navbar } from '@components'
</script>

<App title="Dashboard">
    {#snippet header()}
        <Navbar />
    {/snippet}

    <div class="flex flex-col gap-6 pt-8 flex-1">
        <div class="grid grid-cols-3 gap-8 flex-1">
            {#each Array.from({ length: 9 })}
                <div class="col-span-1 border border-ui-surface-border bg-ui-surface-background opacity-50 p-6">
                    <div class="bg-ui-surface-backgrounds"></div>
                </div>
            {/each}
        </div>
    </div>
</App>