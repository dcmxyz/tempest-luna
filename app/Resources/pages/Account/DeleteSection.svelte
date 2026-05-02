<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Button, TextInput } from '@components'
    import { uri } from '@route'

    const form = useForm({ current_password: '' })
    let confirming = $state(false)

    function submit(e: SubmitEvent) {
        e.preventDefault()

        form.post(uri('/account/delete'), {
            onFinish: () => form.reset(),
        })
    }
</script>

<div class="flex flex-col gap-6 pb-12">
    {#if !confirming}
        <Button variant="danger" onclick={() => confirming = true}>
            Delete account
        </Button>
    {:else}
        <form onsubmit={submit} class="flex flex-col gap-6">
            <TextInput
                id="delete_password"
                name="current_password"
                label="Password"
                type="password"
                autocomplete="current-password"
                error={ form.errors.current_password }
                bind:value={ form.current_password }
            />

            <div class="flex items-center gap-4 mt-2">
                <Button type="submit" variant="danger" disabled={ form.processing }>
                    { form.processing ? 'Please wait…' : 'Confirm delete' }
                </Button>
                <Button variant="secondary" onclick={ () => confirming = false }>Cancel</Button>
            </div>
        </form>
    {/if}
</div>