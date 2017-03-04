import Flashsales from './Flashsales.vue';
import Spinny from '../Spinner.vue';
import Multiselect from 'vue-multiselect';
import OnboardCard from '../OnboardCard.vue';

new Vue({
    el: '#flashsales_index',
    data: {
        flashsales: [],
        search: {
            sellers: [],
            sellers_query: [],
            searching: false,
            filtering: false,
            sale_name: null,
        }
    },
    created(){
        $Bus.$on('fetch:completed', (flashsales)=>{
            this.flashsales = flashsales;
        });
    },
    methods: {
        searchSellers(qry){

        },
        resetSearch(){
            this.search.sale_name = null;
            this.search.sellers = null;
            $Bus.$emit('flashsales:reset');
        },
        performSearch(event){
            this.search.filtering = true;
            $Bus.$emit('flashsales:filter', this.search.sale_name, this.search.sellers);
        },
    },
    components:{
        'spinny': Spinny,
        'flashsales-cards': Flashsales,
        'multiselect': Multiselect,
        'onboard-card' : OnboardCard
    },
})