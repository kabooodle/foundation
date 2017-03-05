/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

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
    }
});