<template>
    <span>
        <inventory-chest v-for="item in inventory"></inventory-chest>
    </span>
</template>
<script>
    import InventoryChest from './InventoryChest.vue';
    export default{
        props: {
            inventory:{
                type: Array,
                default(){
                    return [];
                }
            }
        },
        data(){
            return{

            }
        },
        components:{
            'inventory-chest' : InventoryChest
        }
    }
</script>
