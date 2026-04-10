<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import { Link, Button, TextInput } from "@components";
    import { uri } from "@route";

    const form = useForm({
        email: '',
        password: '',
    })

    function submit(e: SubmitEvent) {
        e.preventDefault()
        form.post(uri('/login'), {
            onFinish: () => form.reset("password"),
        })
    }
</script>

<Auth title="Login">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-6">
            <h1 class="text-4xl font-bold leading-none tracking-tight text-ui-text">
                Log in
            </h1>

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

                <TextInput
                    id="password"
                    name="password"
                    label="Password"
                    type="password"
                    autocomplete="current-password"
                    error={form.errors.password}
                    bind:value={form.password}
                />

                <Button type="submit" disabled={form.processing} class="mt-2">
                    {form.processing ? 'Please wait…' : 'Login'}
                </Button>
            </form>

            <div class="mt-6 pt-6 border-t border-ui-border flex flex-col gap-2">
                <Link href={uri('/forgot-password')} variant="no-visited" class="self-start">
                    Forgot your password?
                </Link>
                <Link href={uri('/register')} variant="no-visited" class="self-start">
                    Create a new account
                </Link>
            </div>
        </div>
    </div>
</Auth>