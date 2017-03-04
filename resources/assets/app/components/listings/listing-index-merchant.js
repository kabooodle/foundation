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

        /**
         *
         * @param endpoint
         * @param listingId
         * @param event
         */
        deleteListingItem(endpoint, listingId, event){
            let $el = $(event.target);

            let successCb = ($noty, response)=>{
                $el.closest('tr[data-id="'+listingId+'"]').remove();
            };

            let notyOptions = {
                text: 'This cannot be undone, are you sure?'
            };

            this.deleteListing(endpoint, successCb, notyOptions);
        },

        /**
         * Delete listing only from facebook.
         *
         * @param {string} endpoint
         * @param {integer} id
         * @param {string} type
         * @param {object} e
         */
        deleteFacebookListing(endpoint, id, e){
            let successCb = ($noty, response)=>{
                $(e.target).closest('tr').replaceWith($(response.body.data.html))
            };

            let notyOptions = {
                text: 'This will queue the listing for removal from Facebook. This cannot be undone, are you sure?'
            };

            this.deleteListing(endpoint, successCb, notyOptions);
        },

        /**
         *
         * @param endpoint
         * @param successCb
         * @param notyOptions
         */
        deleteListing(endpoint, successCb, notyOptions){
            confirmModal(($noty)=>{
                this.actions.deleting = true;
                $noty.$buttons.find('.btn').addClass('disabled').prop('disabled', true);
                this.$http.delete(endpoint).then((response)=>{
                    notify({
                        type: 'success',
                        text: response.body.data.msg
                    });
                    if (successCb instanceof Function){
                        successCb($noty, response);
                    }
                }, (response)=>{
                    notify({
                        text: response.body.data.msg
                    });
                }).finally(()=>{
                    this.actions.deleting = false;
                    $noty.close();
                });
            }, null, notyOptions);
        },
    },
});