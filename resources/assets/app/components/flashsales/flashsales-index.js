import Flashsales from './Flashsales.vue';
import Spinny from '../Spinner.vue';

new Vue({
    el: '#flashsales_index',
    components:{
        'spinny': Spinny,
        'flashsales-cards': Flashsales
    }
})