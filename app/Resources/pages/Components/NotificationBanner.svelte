<script lang="ts">
    import { cm } from '@utils';
    import type {Snippet} from "svelte";

    interface Props {
        type?: 'info' | 'success';
        title?: string;
        message?: string;
        class?: string;
        children?: Snippet;
    }

    let {
        type = 'info',
        title,
        message,
        class: className = '',
        children,
    }: Props = $props();

    const resolvedTitle = $derived(title ?? (type === 'success' ? 'Success' : 'Important'));
    const isSuccess = $derived(type === 'success');
</script>

<div
    role={isSuccess ? 'alert' : 'region'}
    aria-labelledby="notification-banner-title"
    class={cm('border-t-4', isSuccess ? 'border-ui-success' : 'border-ui-info', className)}
>
    <div class={cm('px-4 py-2', isSuccess ? 'bg-ui-success' : 'bg-ui-info')}>
        <p
            id="notification-banner-title"
            class="text-lg font-bold leading-none text-white uppercase tracking-wide"
        >
            {resolvedTitle}
        </p>
    </div>
    <div class={cm('px-4 py-3 border border-t-0', isSuccess ? 'border-ui-success' : 'border-ui-info')}>
        <p class="text-lg leading-6 text-ui-text">
            {message}
            {@render children?.()}
        </p>
    </div>
</div>