<template>
    <span>
        <img :src="img_url" style="margin:-2px 2px 0 0; padding:0;" :height="size" :width="size" >
    </span>
</template>
<script>
    export default {
        props : {
            size : {
                type: String,
                default : '12'
            }
        },
        computed : {
            img_url(){
                return KABOOODLE_APP.makeStaticAsset("assets/images/icons/ring-alt.gif");
            },
        },
    }
</script>
