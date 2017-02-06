/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

new Vue({
    el: '#merchant_listings_index',
    data: {
        actions: {
            deleting: false,
        }
    },
    methods: {
        deleteListingConfirm(endpoint, listingId, event){
            let $el = $(event.target);
            confirmModal(($noty)=>{
                let noty_buttons_parent = $($noty.$buttons[0]);
                noty_buttons_parent.find('.noty-btn').addClass('disabled').prop('disabled', true);

                this.$http.delete(endpoint).then((response)=>{
                    this.actions.deleting = false;
                    $el.closest('tr[data-id="'+listingId+'"]').remove();
                    notify({
                       type:'success',
                        text: response.body.data.msg
                    });
                }, (response)=>{
                    this.actions.deleting = false;
                    notify({
                        text: response.body.data.msg
                    });
                }).finally(()=>{
                    $noty.close();
                    noty_buttons_parent.find('.noty-btn').removeClass('disabled').prop('disabled', false);
                });
            });
        },
    },
    components: {

    }
});