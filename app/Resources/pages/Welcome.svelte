<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from "@layouts";
    import { Link } from "@components";
    import { uri } from "@route";

    interface Props {
        tempestVersion?: string;
        inertiaTempestVersion?: string;
        phpVersion?: string;
        [key: string]: any
    }

    let {
        tempestVersion,
        inertiaTempestVersion,
        phpVersion,
        ...rest
    }: Props = $props();

    const auth = $derived(page.props.auth);
</script>

<App title="Welcome">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-3xl flex flex-col gap-4">
            <header class="flex items-center justify-end px-6 py-3 border border-stone-200">
                <nav class="flex items-center gap-1">
                    {#if auth.user}
                        <Link href={ uri('/logout') } inertiaProps={{ method: 'post' }} class="px-4 py-1.5">Logout</Link>
                    {:else}
                        <Link href={ uri('/login') } class="px-4 py-1.5">Log in</Link>
                        <Link href={ uri('/register') } variant="button">Register</Link>
                    {/if}
                </nav>
            </header>

            <div class="flex flex-col sm:flex-row border border-stone-200">
                <div class="flex-1 bg-white p-8 sm:p-12 flex flex-col justify-between gap-10 border-b sm:border-b-0 sm:border-r border-stone-200">
                    <div>
                        <h1 class="text-4xl sm:text-5xl leading-none tracking-tight text-stone-900 mb-4">
                            <span class="font-black">Luna</span><br>
                            <span class="font-extralight text-stone-400">Starter Kit</span>
                        </h1>

                        <p class="text-sm leading-6 font-light max-w-xs">
                            Auth, profiles, and a place to start.<br> Built on Tempest, Inertia, and Svelte.
                        </p>
                    </div>

                    <p class="font-mono text-xs tracking-widest uppercase text-stone-400">
                        — Tempest PHP {tempestVersion}<br>
                        — Inertia-Tempest {inertiaTempestVersion}<br>
                        — PHP {phpVersion}
                    </p>
                </div>

                <div class="w-full sm:w-80 shrink-0 bg-stone-50 flex items-center justify-center py-12 sm:py-0 min-h-48">
                    <svg viewBox="0 0 160 160" width="136" height="136" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="80" cy="80" r="72" fill="#000"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</App>