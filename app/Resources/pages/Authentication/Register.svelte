<script lang="ts">
    import { useForm } from '@inertiajs/svelte'
    import { Auth } from "@layouts";
    import {Link} from "@components";
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
        <div class="w-full max-w-md flex flex-col gap-4">
            <h1 class="text-4xl leading-none tracking-tight text-stone-900 mb-8">
                <span class="font-black">Create</span><br>
                <span class="font-extralight text-stone-400">your account</span>
            </h1>

            <form onsubmit={submit} class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <input
                        type="text"
                        bind:value={form.name}
                        placeholder="Name"
                        class="px-3 py-2 border border-stone-200 text-sm bg-white placeholder:text-stone-400 focus:outline-none focus:border-stone-900 transition-colors"
                    />
                    {#if form.errors.name}<p class="text-red-700 text-xs font-mono">{form.errors.name}</p>{/if}
                </div>

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
                    {form.processing ? 'Creating account…' : 'Create account'}
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-stone-200 flex flex-col gap-2">
                <Link href={ uri('/login') }>
                    Already have an account? <span class="font-bold text-stone-900">Log in</span>
                </Link>
            </div>
        </div>
    </div>
</Auth>