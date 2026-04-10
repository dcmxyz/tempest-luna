<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import {Button, Link, TextInput, NotificationBanner} from "@components";
    import { uri } from "@route";

    const form = useForm({
        email: '',
    })

    interface Props {
        status?: string,
    }

    let { status = null } = $props();

    function submit(e: SubmitEvent) {
        e.preventDefault()
        form.post(uri('/forgot-password'))
    }
</script>

<Auth title="Forgot password">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-6">
            <h1 class="text-4xl font-bold leading-none tracking-tight text-ui-text">
                Forgot password
            </h1>

            {#if status}
                <NotificationBanner message={status} type="success"/>
            {/if}

            <h2 class="text-xl leading-none tracking-tight text-ui-secondary-text">
                Enter your email address and we will send you a link to reset your password.
            </h2>

            <form onsubmit={submit} class="flex flex-col gap-6">
                <TextInput
                    id="email"
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    error={form.errors.email}
                    bind:value={form.email}
                />

                <Button type="submit" disabled={form.processing} class="mt-2">
                    {form.processing ? 'Please wait…' : 'Email password reset link'}
                </Button>
            </form>

            <div class="mt-6 pt-6 border-t border-ui-border flex flex-col gap-2">
                <Link href={uri('/login')} variant="no-visited" class="self-start">
                    Remembered it? Log in
                </Link>
            </div>
        </div>
    </div>
</Auth>