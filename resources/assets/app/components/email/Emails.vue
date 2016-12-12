<template>
    <div>
        <email-input v-for=></email-input>
    </div>
</template>
<style>

</style>
<script>
    import Email from './Email.vue'
    export default {
        props: {
            newEmailEndpoint: {
                type: String,
                required: true,
            },
        },
        data() {
            return{
                msg:'hello vue'
            }
        },
        components:{
            'email': Email,
        }
    }
</script>
