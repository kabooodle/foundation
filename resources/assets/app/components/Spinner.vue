<template>
    <span>
        <span v-html="img"></span>
    </span>
</template>
<script>
    import spinner from './spinner';
    export default {
        props : {
            size: {
                type: String,
                default: '12'
            }
        },
        computed : {
            img(){
                return spinner(this.size);
            }
        },
    }
</script>
