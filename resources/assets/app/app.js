import Loader from './components/Loading.vue';
import Popover from  './components/Popover.vue';
import PageViewTracker from './components/Pageviewtracker.vue';

new Vue({
    el: '#kabooodle_utilities',
    components: {
        'loader': Loader,
        'pageviewstracker' : PageViewTracker,
        'popout-overlay' : Popover,
    }
});