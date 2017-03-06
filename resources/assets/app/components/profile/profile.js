import Followable from '../follow/Followable.vue';
import MessageUser from '../messenger/MessageUser.vue';

new Vue({
    el: '#profilePage',
    components : {
        'followable': Followable,
        'message-user' : MessageUser
    },
});