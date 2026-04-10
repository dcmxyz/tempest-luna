<script lang="ts">
    import { cm } from "@utils";
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { Button } from "@components";

    interface Props {
        id: string;
        name: string;
        label: string;
        hint?: string;
        error?: string;
        value?: string;
        type?: string;
        autocomplete?: HTMLInputAttributes['autocomplete'];
        disabled?: boolean;
        class?: string;
        [key: string]: any;
    }

    let {
        id,
        name,
        label,
        hint = undefined,
        error = undefined,
        value = $bindable(''),
        type = 'text',
        autocomplete = undefined,
        disabled = false,
        class: className = '',
        ...rest
    }: Props = $props();

    const isPassword = type === 'password';
    let showPassword  = $state(false);
    let resolvedType  = $derived(isPassword ? (showPassword ? 'text' : 'password') : type);

    const hintId  = `${id}-hint`;
    const errorId = `${id}-error`;

    const describedBy = $derived([
        hint  ? hintId  : null,
        error ? errorId : null,
    ].filter(Boolean).join(' ') || undefined);
</script>

<div class={cm("flex flex-col gap-2", error ? "border-l-4 border-ui-error pl-3" : "", className)}>
    <label for={id} class="block text-lg leading-none text-ui-text">
        {label}
    </label>

    {#if hint}
        <div id={hintId} class="text-base leading-none text-ui-secondary-text">
            {hint}
        </div>
    {/if}

    {#if error}
        <p id={errorId} class="text-base leading-none text-ui-error">
            <span class="sr-only">Error:</span>
            {error}
        </p>
    {/if}

    <div class={isPassword  ? "flex items-center gap-2" : ""}>
        <input
            formnovalidate
            {id}
            {name}
            type={resolvedType}
            {disabled}
            {autocomplete}
            bind:value
            aria-describedby={describedBy}
            class={cm(
                "w-full px-0.5 py-2 text-lg leading-none tracking-wide text-ui-text bg-white",
                "border-2 border-ui-input-border",
                "focus:outline-4 focus:outline-ui-focus focus:outline-offset-0",
                error    ? "border-ui-error" : "",
                disabled ? "opacity-50 cursor-not-allowed" : "",
            )}
            {...rest}
        />

        {#if isPassword}
            <Button
                type="button"
                variant="secondary"
                aria-controls={id}
                aria-label={showPassword ? 'Hide password' : 'Show password'}
                onclick={() => showPassword = !showPassword}
                class="min-w-20"
            >
                {showPassword ? 'Hide' : 'Show'}<span class="sr-only"> password</span>
            </Button>
        {/if}
    </div>
</div>