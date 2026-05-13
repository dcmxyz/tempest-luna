<script lang="ts">
    import { useHttp } from '@inertiajs/svelte'
    import {Badge, Link} from '@components'
    import { uri } from '@route'

    let userSessions: Array<any> = $state([]);

    const http = useHttp();
    http.get(uri('/account/sessions'), {
        onSuccess: (response) => {
            userSessions = response ? response?.value : [];
        },
    })
</script>

<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold leading-none text-ui-text">Recent activity</h3>
        {#if userSessions.length > 1}
            <Link
                href={ uri('/account/sessions/logout') }
                inertiaProps={{ method: 'post', preserveState: false }}
                variant="no-visited"
            >
                Log out of all other devices
            </Link>
        {/if}
    </div>
    <div>
        <div class="overflow-x-auto px-0 mx-0">
            <table class="min-w-full divide-y-2 divide-ui-input-border">
                <thead class="ltr:text-left rtl:text-right">
                    <tr class="*:font-medium *:text-ui-text">
                        <th class="py-2 whitespace-nowrap">Client</th>
                        <th class="py-2 whitespace-nowrap">Last active</th>
                        <th class="py-2 whitespace-nowrap"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-input-border">
                    {#each userSessions as session}
                        <tr class="*:text-ui-text *:first:font-medium">
                            <td class="py-2 whitespace-nowrap">
                                {session.user_agent} <br>
                                <span class="font-light">{session.ip_address}</span>
                            </td>
                            <td class="py-2 whitespace-nowrap">{session.last_active_at}</td>
                            <td class="py-2 whitespace-nowrap text-right">
                                {#if session.is_current_device}
                                    <Badge>
                                        This device
                                    </Badge>
                                {/if}
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>