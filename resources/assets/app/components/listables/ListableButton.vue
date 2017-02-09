<template>
    <button
            class="btn white btn-xs"
            style="margin-right: 3px;"
        >
        <input type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;">
        <slot></slot>
    </button>
</template>
<script>
    export default{
        props: {
            click_event: {

            }
        },
    }
</script>
