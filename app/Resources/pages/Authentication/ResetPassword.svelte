<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import {Button, Link, TextInput} from "@components";
    import { uri } from "@route";

    const { token }: { token: string } = $props();

    const form = useForm({
        token: token,
        password: '',
    })

    function submit(e: SubmitEvent) {
        e.preventDefault()
        form.post(uri('/reset-password'), {
            onFinish: () => form.reset("password"),
        })
    }
</script>

<Auth title="Reset password">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-6">
            <h1 class="text-4xl font-bold leading-none tracking-tight text-ui-text">
                Reset your password
            </h1>

            <form onsubmit={submit} class="flex flex-col gap-6">
                <TextInput
                    id="password"
                    name="password"
                    label="Password"
                    type="password"
                    autocomplete="new-password"
                    error={form.errors.password}
                    hint="At least 12 characters, one uppercase, one lowercase letter, and one number"
                    bind:value={form.password}
                />

                <Button type="submit" disabled={form.processing} class="mt-2">
                    {form.processing ? 'Please wait…' : 'Reset password'}
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