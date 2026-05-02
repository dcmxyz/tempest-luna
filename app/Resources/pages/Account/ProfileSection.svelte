<script lang="ts">
    import { page, useForm } from '@inertiajs/svelte'
    import { Alert, Button, Link, NotificationBanner, TextInput, UserAvatar } from '@components'
    import { uri } from '@route'

    const auth = $derived(page.props.auth)

    const nameForm = useForm({ name: auth.user?.name })
    const emailForm = useForm({ email: auth.user?.email })

    const emailVerified = $derived(auth.user?.email_verified_at !== null)

    function submit(e: SubmitEvent, form: typeof emailForm | typeof nameForm ) {
        e.preventDefault()
        if (form.processing) return

        form.post(form === nameForm ? uri('/account/name') : uri('/account/email'))
    }
</script>

<div class="flex flex-col gap-12">
    <div>
        <form onsubmit={ (e) => submit(e, nameForm) } class="flex flex-col gap-3">
            <div class="flex items-center">
                <TextInput
                    id="name"
                    name="name"
                    label="Name"
                    type="text"
                    autocomplete="name"
                    class="flex-1"
                    error={ nameForm.errors.name }
                    hint="Your avatar will automatically change when you update your name."
                    bind:value={ nameForm.name }
                />
                <div class="ml-4 pt-12">
                    <UserAvatar name={ nameForm.name } />
                </div>
            </div>

            <Button type="submit" disabled={ nameForm.processing }>
                { nameForm.processing ? 'Please wait…' : 'Update name' }
            </Button>

            {#if nameForm.recentlySuccessful}
                <Alert variant="success" message="Your name has been updated successfully."/>
            {/if}
        </form>
    </div>

    <div class="border-t border-ui-border"></div>

    <div>
        <!--TODO: Add email verification-->
        {#if !emailVerified}
            <NotificationBanner
                type="info"
                title="Email not verified"
                message="Please verify your email address. A verification link has been sent to your email address."
                class="mb-6"
            >
                <Link href={'#'/*uri('/account/resend')*/} inertiaProps={{ method: 'post' }} variant="no-visited">
                    Resend verification
                </Link>
            </NotificationBanner>
        {/if}

        <form onsubmit={ (e) => submit(e, emailForm) } class="flex flex-col gap-3">
            <TextInput
                id="email"
                name="email"
                label="Email"
                type="email"
                autocomplete="email"
                error={ emailForm.errors.email }
                bind:value={ emailForm.email }
            />

            <Button type="submit" disabled={ emailForm.processing }>
                { emailForm.processing ? 'Please wait…' : 'Update email' }
            </Button>

            {#if emailForm.recentlySuccessful}
                <Alert variant="success" message="Your email has been updated successfully."/>
            {/if}
        </form>
    </div>
</div>