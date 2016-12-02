import Commentable from '../comments/Commentable.vue';
import Followable from '../follow/Followable.vue';
import Spinny from  '../Spinner.vue';

Vue.component('spinny', Spinny);

new Vue({
    el: '#listing-item-page',
    components : {
        'comments-index' : Commentable,
        'followable' : Followable
    },
});