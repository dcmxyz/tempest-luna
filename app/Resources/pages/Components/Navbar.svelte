<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { Button, Link } from '@components'
    import { uri } from '@route'

    const auth = $derived(page.props.auth)
    const firstName = $derived(auth.user?.name?.split(' ')[0])
</script>

<header class="-m-4 mb-0">
    <div class="bg-cod-200 px-6 py-4">
        <span class="font-serif text-xl font-black leading-none text-ui-text">Luna</span>
    </div>

    <div class="bg-ui-surface-background border-b border-ui-border px-6 flex items-stretch gap-x-6 min-h-10">
        {#if auth.user}
            <Link
                href={uri('/dashboard')}
                variant="no-visited"
                class="flex items-center text-sm border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline hover:underline"
            >
                My account ({firstName ?? ''})
            </Link>

            <Link
                href={uri('/logout')}
                inertiaProps={{ method: 'post' }}
                variant="no-visited"
                class="flex items-center text-sm border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline hover:underline"
            >
                Logout
            </Link>
        {/if}
    </div>
</header>