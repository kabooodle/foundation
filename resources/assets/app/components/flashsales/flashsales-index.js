import Flashsales from './Flashsales.vue';
import Spinny from '../Spinner.vue';
import Multiselect from 'vue-multiselect';

new Vue({
    el: '#flashsales_index',

    data: {

        search: {
            sellers: [],
            sellers_query: [],
            searching: false,
            filtering: false,
            sale_name: null,
        }
    },
    methods: {
        searchSellers(qry){

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
    },
})