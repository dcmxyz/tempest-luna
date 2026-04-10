<script lang="ts">
    import { cm } from '@utils';

    interface Props {
        type?: 'info' | 'success';
        title?: string;
        message: string;
        class?: string;
    }

    let {
        type = 'info',
        title,
        message,
        class: className = '',
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
        </p>
    </div>
</div>