<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Alert, Button, TextInput } from '@components'
    import { uri } from '@route'

    const form = useForm({
        current_password: '',
        password: '',
    })

    function submit(e: SubmitEvent) {
        e.preventDefault()
        if (form.processing) return

        form.post(uri('/account/password'), {
            onSuccess: () => form.reset(),
            onError: () => {
                if (form.errors.password) {
                    form.password = "";
                }
                
                if (form.errors.current_password) {
                    form.current_password = "";
                }
            },
        })
    }
</script>

<form onsubmit={ submit } class="flex flex-col gap-6">
    <TextInput
        id="current_password"
        name="current_password"
        label="Current password"
        type="password"
        autocomplete="current-password"
        error={ form.errors.current_password }
        bind:value={ form.current_password }
    />

    <TextInput
        id="password"
        name="password"
        label="New password"
        type="password"
        autocomplete="new-password"
        error={ form.errors.password }
        hint="At least 12 characters, one uppercase, one lowercase letter, and one number"
        bind:value={ form.password }
    />

    <Button type="submit" disabled={ form.processing } class="mt-2">
        { form.processing ? 'Please wait…' : 'Update password' }
    </Button>

    {#if form.recentlySuccessful}
        <Alert variant="success" message="Your password has been updated successfully."/>
    {/if}
</form>