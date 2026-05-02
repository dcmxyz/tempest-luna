<script lang="ts">
    import { page } from '@inertiajs/svelte'
    import { App } from '@layouts'
    import { Navbar } from '@components'
    import ProfileSection from '@pages/Account/ProfileSection.svelte'
    import PasswordSection from '@pages/Account/PasswordSection.svelte'
    import DeleteSection from '@pages/Account/DeleteSection.svelte'

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

    <div class="flex flex-col gap-12 pt-8 max-w-2xl">
        <ProfileSection />
        <div class="border-t border-ui-border"></div>
        <PasswordSection />
        <div class="border-t border-ui-border"></div>
        <DeleteSection />
    </div>
</App>