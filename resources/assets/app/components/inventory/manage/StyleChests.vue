<template>
    <span>
        <style-chest v-for="style in inventory_items"></style-chest>
    </span>
</template>
<script>
    import StyleChest from './StyleChest.vue';
    export default{
        data(){
            return{

            }
        },
        components:{
            'style-chest' : StyleChest
        }
    }
</script>
