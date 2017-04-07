/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */


import Comments from '../comments/Commentable.vue';
import ListableShareLink from './show/ListableShareLink.vue';
import Modal from '../Modal.vue';
import Multiselect from 'vue-multiselect';
import Spinny from '../Spinner.vue';
import VCard from '../users/V-Card.vue';
import OwnerClaim from '../claims/OwnerClaim.vue';

new Vue({
    el: '#listable_item_show',
    components: {
        'comments-index' : Comments,
        'listable-share' : ListableShareLink,
        'modal' : Modal,
        'multiselect' : Multiselect,
        'v-card' : VCard,
        'spinny' : Spinny,
        'owner-claim' : OwnerClaim
    }
});