<script lang="ts">
    import AvatarMarble from "svelte-boring-avatars";

    interface Props {
        name?: string;
        size?: number;
    }

    let { name = "default", size = 40 }: Props = $props();
</script>

<div class="grayscale-50">
    <AvatarMarble {size} {name} square={true}/>
</div>