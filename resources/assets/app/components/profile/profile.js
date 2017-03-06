import Followable from '../follow/Followable.vue';
import MessageUser from '../messenger/MessageUser.vue';
import Followers from './Follows.vue';
import Following from './Follows.vue';

new Vue({
    el: '#profilePage',
    components : {
        'followable': Followable,
        'message-user' : MessageUser,
        'followers' : Followers,
        'following' : Following
    },
});