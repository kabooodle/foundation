<template>
    <div>
        <header-component/>
        <div>this is template body</div>
        <other-component/>
    </div>
</template>
<script>
    import VCard from '../users/V-Card.vue';
    export default{
        props: {

        },
        data(){
            return{
                msg:'hello vue'
            }
        },
        components:{
            'v-card' : VCard
        }
    }
</script>
