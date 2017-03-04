/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Spinny from '../Spinner.vue';
new Vue({
    el: '#merchant_listings_detailed_items',
    data: {
        actions: {
            deleting:false
        }
    },
    methods: {
        selectAll(e){
            const $itemEls = $('input.listing-item-checkbox');
            $itemEls.prop('checked', e.target.checked);
        },
        /**
         *
         * @param endpoint
         * @param id
         * @param type
         * @param e
         */
        deleteListingItem(endpoint, id, type, e){
            let successCb = ($noty, response)=>{
                $(e.target).closest('tr').remove();
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
        deleteFacebookListingItem(endpoint, id, type, e){
            let successCb = ($noty, response)=>{
                $(e.target).closest('tr').replaceWith($(response.body.data.html))
            };

            let notyOptions = {
                text: 'This will queue the listing for removal from Facebook. This cannot be undone, are you sure?'
            };

            this.deleteListing(endpoint, successCb, notyOptions);
        },
        /**
         * Final method used by various methods for making delete request.
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
    components: {
        'spinny' : Spinny
    }
});