<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import {Button, Link, TextInput} from "@components";
    import { uri } from "@route";

    const form = useForm({
        name: '',
        email: '',
        password: '',
    })

    function submit(e: SubmitEvent) {
        e.preventDefault()
        form.post(uri('/register'), {
            onFinish: () => form.reset("password"),
        })
    }
</script>

<Auth title="Register">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-6">
            <h1 class="text-4xl font-bold leading-none tracking-tight text-ui-text">
                Create an account
            </h1>

            <form onsubmit={submit} class="flex flex-col gap-6">
                <TextInput
                    id="name"
                    name="name"
                    label="Name"
                    type="text"
                    autocomplete="name"
                    error={form.errors.name}
                    bind:value={form.name}
                />

                <TextInput
                    id="email"
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    error={form.errors.email}
                    bind:value={form.email}
                />

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
                    {form.processing ? 'Please wait…' : 'Create account'}
                </Button>
            </form>

            <div class="mt-6 pt-6 border-t border-ui-border flex flex-col gap-2">
                <Link href={uri('/login')} variant="no-visited" class="self-start">
                    Already have an account? Log in
                </Link>
            </div>
        </div>
    </div>
</Auth>