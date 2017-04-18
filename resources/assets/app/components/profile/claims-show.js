import CancelClaim from '../claims/CancelClaim.vue';
import VCard from '../users/V-Card.vue';

new Vue({
    el: '#claims_page',
    components: {
        'cancel-claim': CancelClaim,
        'v-card' : VCard
    }
});
