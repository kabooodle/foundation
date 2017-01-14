<template>
    <span>
        <button
                class="btn white btn-xs"
                style="margin-right: 3px;"
        >
            <input  type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;">
            <span class="text-md">{{ name }}</span>
            <small class="text-sm text-muted block" style="margin-top: -2px;">({{ count }})</small>
        </button>
    </span>
</template>
<script>
    export default{
        props: {
            name: {
                required: true,
            },
            count: {
                required: true,
            }
        },
        data(){
            return{
            }
        },
    }
</script>
