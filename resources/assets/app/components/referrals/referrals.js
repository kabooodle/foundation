/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import ReferralCards from './ReferralCards.vue';

new Vue({
    el: '#referrals_index',
    created(){
        this.$nextTick(()=>{
            clippy('[data-clipboard-target]');
        });
    },
    methods: {
        triggerFbShare(url, e){
            FB.ui({
                method: 'share',
                display: 'popup',
                href: url,
            }, function(response){ });
        },
    },
    components: {
        'referral-cards' : ReferralCards
    }
});