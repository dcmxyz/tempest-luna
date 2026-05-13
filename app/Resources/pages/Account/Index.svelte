<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from '@layouts'
    import { Navbar } from '@components'
    import ProfileSection from '@pages/Account/ProfileSection.svelte'
    import PasswordSection from '@pages/Account/PasswordSection.svelte'
    import DeleteSection from '@pages/Account/DeleteSection.svelte'
    import SessionsSection from "@pages/Account/SessionsSection.svelte";

    interface Props {
        [key: string]: any
    }

    let { ...rest }: Props = $props()

    const auth = $derived(page.props.auth)
</script>

<App title="My account">
    {#snippet header()}
        <Navbar />
    {/snippet}

    <div class="flex flex-col gap-8 max-w-2xl">
        <h2 class="text-xl font-black leading-none text-ui-text pb-1.5">Your account</h2>
        <ProfileSection />
        <div class="border-t border-ui-border"></div>
        <h2 class="text-xl font-black leading-none text-ui-text pb-1.5">Security</h2>
        <PasswordSection />
        <div class="border-t border-ui-border"></div>
        <SessionsSection />
        <div class="border-t border-ui-border"></div>
        <h2 class="text-xl font-black leading-none text-ui-text pb-1.5">Privacy</h2>
        <DeleteSection />
    </div>
</App>