/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Spinny from './Spinner.vue';

new Vue({
    el: '#closed-beta',
    data: {
        email: '',
        actions: {
            saving: false,
        }
    },
    methods: {
        send(endpoint){
            this.actions.saving = true;
            this.resetAlerts();

            if (! this.email || this.email == '') {
                this.createNotice('The email must be a valid email address.', 'error');
                this.actions.saving = false;
                return false;
            }

            this.$http.post(endpoint, {email: this.email}).then((response)=>{
                this.createNotice(response.body.data.msg, 'success');
            }, (response)=>{
                this.createNotice(response.body.data.msg, 'error');
            }).finally(()=>{
                this.actions.saving = false;
                this.email = '';
            })
        },
        resetAlerts(){
            $('.noty_inline_layout_container').remove();
        },
        createNotice(msg, type){
            notify({
                el: $('#form-alerts'),
                text: msg,
                type: type
            });
        },
    },
    components: {
        'spinny' : Spinny
    }
});