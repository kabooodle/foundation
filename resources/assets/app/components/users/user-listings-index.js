import UserListings from './UserListings.vue';
import Follow from '../follow/Followable.vue';
import Watch from '../watch/Watchable.vue';

new Vue({
    el: '#user_listings_index',
    components: {
        'user-listings' : UserListings,
        'follow' : Follow,
        'watch' : Watch
    }
});