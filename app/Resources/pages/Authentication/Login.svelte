<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import { Link } from "@components";
    import { uri } from "@route";

    const form = useForm({
        email: '',
        password: '',
    })

    function submit(e: SubmitEvent) {
        e.preventDefault()
        form.post(uri('/login'))
    }
</script>

<Auth title="Login">
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md flex flex-col gap-4">
            <h1 class="text-4xl leading-none tracking-tight text-stone-900 mb-8">
                <span class="font-black">Login</span><br>
                <span class="font-extralight text-stone-400">into your account</span>
            </h1>

            <form onsubmit={submit} class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <input
                        type="email"
                        bind:value={form.email}
                        placeholder="Email"
                        class="px-3 py-2 border border-stone-200 text-sm bg-white placeholder:text-stone-400 focus:outline-none focus:border-stone-900 transition-colors"
                    />
                    {#if form.errors.email}<p class="text-red-700 text-xs font-mono">{form.errors.email}</p>{/if}
                </div>

                <div class="flex flex-col gap-1">
                    <input
                        type="password"
                        bind:value={form.password}
                        placeholder="Password"
                        class="px-3 py-2 border border-stone-200 text-sm bg-white placeholder:text-stone-400 focus:outline-none focus:border-stone-900 transition-colors"
                    />
                    {#if form.errors.password}<p class="text-red-700 text-xs font-mono">{form.errors.password}</p>{/if}
                </div>

                <button
                    type="submit"
                    disabled={form.processing}
                    class="mt-2 px-4 py-2 bg-stone-900 text-white text-sm font-bold hover:bg-stone-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                    {form.processing ? 'Please wait…' : 'Login'}
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-stone-200 flex flex-col gap-2">
                <Link href={ uri('/forgot-password') }>
                    Forgot your password?
                </Link>
                <Link href={ uri('/register') }>
                    Need a new account? <span class="font-bold text-stone-900">Register</span>
                </Link>
            </div>
        </div>
    </div>
</Auth>