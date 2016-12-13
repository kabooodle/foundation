<template>
    <div>
        <email v-for="email in emails"
            :is-input=false
            :address=email.address
            :is-primary=email.primary
            :is-verified=email.verified
        ></email>
    </div>
</template>
<style>

</style>
<script>
    import Email from './Email.vue'
    export default {
        props: {
            emails: Array,
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
        },
        created: function () {
            console.log(this.emails);
        },
    }
</script>
