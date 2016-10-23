import Loader from './components/Loading.vue';
import Popover from  './components/Popover.vue';

new Vue({
    el: '#kabooodle_utilities',
    components: {
        'loader': Loader,
        'popout-overlay' : Popover
    }
});