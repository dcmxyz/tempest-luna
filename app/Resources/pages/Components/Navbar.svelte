<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { Button, Link } from '@components'
    import {uri, uriIs} from '@route'

    const auth = $derived(page.props.auth)
    const firstName = $derived(auth.user?.name?.split(' ')[0])
</script>

<header class="-m-4 mb-0">
    <div class="px-6 py-4">
        <span class="font-serif text-xl font-black leading-none text-ui-text">Luna</span>
    </div>

    <div class="px-6 flex items-stretch gap-x-6 min-h-12 bg-ui-surface-background border-t border-b border-ui-surface-border">
        <Link
            href={uri('/dashboard')}
            variant="no-visited"
            class={
                uriIs('/dashboard')
                    ? 'border-b-4 border-ui-brand  transition-colors no-underline pt-3 px-0.5'
                    : 'border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3 px-0.5'
            }
        >
            Dashboard
        </Link>

        {#if auth.user}
            <Link
                href={uri('/dashboard')}
                variant="no-visited"
                class="border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3 px-0.5"
            >
                My account ({firstName ?? ''})
            </Link>

            <Link
                href={uri('/logout')}
                inertiaProps={{ method: 'post' }}
                variant="no-visited"
                class="border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3 px-0.5"
            >
                Logout
            </Link>
        {/if}
    </div>
</header>