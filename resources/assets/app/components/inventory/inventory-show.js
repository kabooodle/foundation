/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */


import Comments from '../comments/Commentable.vue';
import InventoryShareLink from './show/InventoryShareLink.vue';
import Modal from '../Modal.vue';
import Multiselect from 'vue-multiselect';
import Spinny from '../Spinner.vue';
import VCard from '../users/V-Card.vue';

new Vue({
    el: '#inventory_item_show',
    components: {
        'comments-index' : Comments,
        'inventory-share' : InventoryShareLink,
        'modal' : Modal,
        'multiselect' : Multiselect,
        'v-card' : VCard,
        'spinny' : Spinny
    }
});