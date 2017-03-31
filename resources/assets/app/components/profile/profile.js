
import Followable from '../follow/Followable.vue';
import ListingsList from '../listings/ListingsList.vue';
import ClaimsList from '../claims/ClaimsList.vue';
import MessageUser from '../messenger/MessageUser.vue';
import Followers from './Follows.vue';
import Following from './Follows.vue';

new Vue({
    el: '#profilePage',
    components : {
        'claims-list': ClaimsList,
        'listings-list': ListingsList,
        'followable': Followable,
        'message-user' : MessageUser,
        'followers' : Followers,
        'following' : Following
    },
});