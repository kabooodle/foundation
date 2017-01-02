import CheckIn from '../check-in/CheckIn.vue';
import Commentable from '../comments/Commentable.vue';
import Followable from '../follow/Followable.vue';
import Spinny from  '../Spinner.vue';
import MessageUser from '../messenger/MessageUser.vue';

Vue.component('spinny', Spinny);

new Vue({
    el: '#listing-item-page',
    components : {
        'check-in' : CheckIn,
        'comments-index' : Commentable,
        'followable' : Followable,
        'message-user' : MessageUser
    },
});