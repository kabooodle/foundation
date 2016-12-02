<template>
    <span>
        <img src="/assets/images/icons/ring-alt.gif" style="margin:-2px 2px 0 0; padding:0;" :height="size" :width="size" >
    </span>
</template>
<script>
    export default {
        props : {
            size : {
                type: String,
                default : '12'
            }
        }
    }
</script>
