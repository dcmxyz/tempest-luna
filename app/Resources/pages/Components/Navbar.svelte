<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { Link, UserAvatar } from '@components'
    import { uri, uriIs } from '@route'

    const auth = $derived(page.props.auth)
    const firstName = $derived(auth.user?.name?.split(' ')[0])
</script>

<header class="-m-4 mb-0">
    <div class="px-4 py-4">
        <span class="font-serif text-xl font-black leading-none text-ui-text">Luna</span>
    </div>

    <div class="px-4 flex items-stretch gap-x-6 min-h-12 bg-ui-surface-background border-t border-b border-ui-surface-border">
        {#if auth.user}
            <Link
                    href={ uri('/') }
                    variant="no-visited"
                    class={
                uriIs('/')
                    ? 'border-b-4 border-ui-brand  transition-colors no-underline pt-3.5 px-0.5'
                    : 'border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5'
                }
            >
                Welcome
            </Link>

            <Link
                href={ uri('/dashboard') }
                variant="no-visited"
                class={
                    uriIs('/dashboard')
                        ? 'border-b-4 border-ui-brand  transition-colors no-underline pt-3.5 px-0.5'
                        : 'border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5'
                }
            >
                Dashboard
            </Link>

            <div class="flex-1"></div>

            <Link
                href={ uri('/account') }
                variant="no-visited"
                class={
                    uriIs('/account*')
                        ? 'border-b-4 border-ui-brand  transition-colors no-underline pt-3.5 px-0.5'
                        : 'border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5'
                }
            >
                <span class="flex">My account (&nbsp;{firstName ?? ''}&nbsp;<UserAvatar name={auth.user?.name} size={18} />&nbsp;)</span>
            </Link>

            <Link
                href={ uri('/logout') }
                inertiaProps={{ method: 'post' }}
                variant="no-visited"
                class="border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5"
            >
                Logout
            </Link>
        {:else}
            <Link
                href={ uri('/') }
                variant="no-visited"
                class={
                uriIs('/')
                    ? 'border-b-4 border-ui-brand  transition-colors no-underline pt-3.5 px-0.5'
                    : 'border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5'
                }
            >
                Welcome
            </Link>

            <div class="flex-1"></div>

            <Link
                href={ uri('/login') }
                variant="no-visited"
                class="border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5"
            >
                Log in
            </Link>

            <Link
                href={ uri('/register') }
                variant="no-visited"
                class="border-b-4 border-transparent hover:border-ui-brand transition-colors no-underline pt-3.5 px-0.5"
            >
                Create an account
            </Link>
        {/if}
    </div>
</header>