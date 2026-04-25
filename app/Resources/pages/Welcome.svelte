<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from "@layouts";
    import {Button, Link} from "@components";
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

    const hour = new Date().getHours();
    const greeting = hour < 6 ? 'Good night' : hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';

    const firstName = $derived(auth.user?.name?.split(' ')[0]);
</script>

<App title="Welcome">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-3xl flex flex-col gap-4">
            <header class="flex items-center justify-between pl-12 pr-6 py-3 border border-ui-border bg-ui-surface-background">
                <div class="text-lg leading-none font-serif">
                    {#if auth.user}
                        { greeting }, { firstName }.
                    {/if}
                </div>
                <nav class="flex items-center gap-x-6">
                    {#if auth.user}
                        <Link href={ uri('/logout') } inertiaProps={{ method: 'post' }} variant="no-visited">Logout</Link>
                        <Button>Go to dashboard</Button>
                    {:else}
                        <Link href={ uri('/login') } variant="no-visited">Log in</Link>
                        <Button href={ uri('/register') } variant="primary">Create an account</Button>
                    {/if}
                </nav>
            </header>

            <div class="flex flex-col sm:flex-row border border-ui-border">
                <div class="flex-1 p-8 sm:p-12 flex flex-col justify-between gap-10 border-b sm:border-b-0 sm:border-r border-ui-border">
                    <div>
                        <h1 class="text-4xl sm:text-5xl leading-none tracking-tight text-ui-text mb-6 font-serif">
                            <span class="font-black">Luna</span><br>
                            <span class="font-extralight">Starter Kit</span>
                        </h1>

                        <p class="text-ui-secondary-text max-w-xs">
                            Auth, profiles, and a place to start.<br>
                            Built on Tempest, Inertia, and Svelte.
                        </p>
                    </div>

                    <p class="font-mono text-xs tracking-widest uppercase text-ui-secondary-text">
                        Tempest PHP {tempestVersion}<br>
                        Inertia-Tempest {inertiaTempestVersion}<br>
                        PHP {phpVersion}
                    </p>
                </div>

                <div class="w-full sm:w-80 shrink-0 fill-ui-brand flex items-center justify-center py-12 sm:py-0 min-h-48 bg-ui-surface-background">
                    <svg viewBox="0 0 160 160" width="160" height="160" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="80" cy="80" r="72" fill="var(--color-ui-brand)"/>

                        <g transform="translate(115, 38)">
                            <line x1="0" y1="-6" x2="0" y2="6" stroke="var(--color-cod-100)" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="-6" y1="0" x2="6" y2="0" stroke="var(--color-cod-100)" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="-3.5" y1="-3.5" x2="3.5" y2="3.5" stroke="var(--color-cod-100)" stroke-width="0.6" stroke-linecap="round"/>
                            <line x1="3.5" y1="-3.5" x2="-3.5" y2="3.5" stroke="var(--color-cod-100)" stroke-width="0.6" stroke-linecap="round"/>
                        </g>

                        <g transform="translate(42, 52)">
                            <line x1="0" y1="-3.5" x2="0" y2="3.5" stroke="var(--color-cod-100)" stroke-width="0.8" stroke-linecap="round"/>
                            <line x1="-3.5" y1="0" x2="3.5" y2="0" stroke="var(--color-cod-100)" stroke-width="0.8" stroke-linecap="round"/>
                        </g>

                        <circle cx="85" cy="80" r="34" fill="var(--color-cod-100)"/>
                        <circle cx="110" cy="75" r="32" fill="var(--color-ui-brand)"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</App>
