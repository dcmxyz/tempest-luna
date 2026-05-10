<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { Link, UserAvatar } from '@components'
    import { uri, uriIs } from '@route'

    const auth = $derived(page.props.auth)
    const firstName = $derived(auth.user?.name?.split(' ')[0])
</script>

<header class="border-r border-ui-surface-border w-72 h-full justify-between flex flex-col">
    <div class="p-4">
        <span class="font-mono text-xl font-black leading-none text-ui-text">Luna</span>
    </div>

    <div class="gap-2 pb-4 pt-2 flex flex-col flex-1 items-stretch gap-x-6 min-h-12 bg-ui-surface-background border-t border-ui-surface-border">
        {#if auth.user}
            <Link
                href={ uri('/') }
                variant="no-visited"
                class={
                uriIs('/')
                    ? 'border-l-4 py-2.5 px-4 border-ui-link-border bg-ui-link-border/10 transition-colors no-underline'
                    : 'border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline'
                }
            >
                Welcome
            </Link>

            <Link
                href={ uri('/dashboard') }
                variant="no-visited"
                class={
                    uriIs('/dashboard')
                        ? 'border-l-4 py-2.5 px-4 border-ui-link-border bg-ui-link-border/10 transition-colors no-underline'
                        : 'border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline'
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
                        ? 'border-l-4 py-2.5 px-4 border-ui-link-border bg-ui-link-border/10 transition-colors no-underline'
                        : 'border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline'
                }
            >
                <div class="flex">
                    <span class="mr-2"><UserAvatar name={auth.user?.name} size={18} /></span>
                    {firstName ?? ''}
                </div>
            </Link>

            <Link
                href={ uri('/logout') }
                inertiaProps={{ method: 'post' }}
                variant="no-visited"
                class="border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline"
            >
                Logout
            </Link>
        {:else}
            <Link
                href={ uri('/') }
                variant="no-visited"
                class={
                uriIs('/')
                    ? 'border-l-4 py-2.5 px-4 border-ui-link-border bg-ui-link-border/10 transition-colors no-underline'
                    : 'border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline'
                }
            >
                Welcome
            </Link>

            <div class="flex-1"></div>

            <Link
                href={ uri('/login') }
                variant="no-visited"
                class="border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline"
            >
                Log in
            </Link>

            <Link
                href={ uri('/register') }
                variant="no-visited"
                class="border-l-4 py-2.5 px-4 border-transparent hover:border-ui-link-border transition-colors no-underline"
            >
                Create an account
            </Link>
        {/if}
    </div>
</header>