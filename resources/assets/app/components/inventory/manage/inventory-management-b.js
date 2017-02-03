import InventoryChest from './InventoryChest.vue';
import OnboardCard from '../../OnboardCard.vue';

new Vue({
    el: '#manage_inventory',
    props: {
        inventory_endpoint: {
            default(){
                return inventory_endpoint;
            }
        }
    },
    data: {
        actions: {
            fetching_inventory: false
        },
        inventory: []
    },
    methods: {
        getInventory(){

        },
    },
    components: {
        'inventory-chest' : InventoryChest,
        'onboard-card' : OnboardCard
    }
});