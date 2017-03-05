/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Spinny from './Spinner.vue';

new Vue({
    el: "#home-container",
    data: {
        contact_fields: {
            email: '',
            name: '',
            message: '',
        },
        actions: {
            saving: false,
        }
    },
    methods: {
        sendContact(route){
            this.actions.saving = true;
            this.$http.post(route, this.contact_fields).then((response)=>{
                this.createNotice(response.body.data.msg, 'success');
                this.contact_fields.email = '';
                this.contact_fields.name = '';
                this.contact_fields.message = '';
            }, (response)=>{
                this.createNotice(response.body.data.msg, 'error');
            }).finally(()=>{
                this.actions.saving = false;
            });
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
    mounted(){

        this.$nextTick(()=>{

            $('#contact_form').on('hidden.bs.modal', (e)=>{
                this.contact_fields.email = '';
                this.contact_fields.name = '';
                this.contact_fields.message = '';
                this.actions.saving = false;
                this.resetAlerts();
            });

            var fireRefreshEventOnWindow = function () {
                var evt = document.createEvent("HTMLEvents");
                evt.initEvent('resize', true, false);
                window.dispatchEvent(evt);
            };

            let collapseable_toggler = $('[data-toggle="collapseable"]');
            let collapseable_el = $('.collapseable');
            fireRefreshEventOnWindow();

            collapseable_toggler.click(function () {
                collapseable_el.is(':visible') ? collapseable_el.removeClass('show').hide() : collapseable_el.addClass('show').show();
            });

            let pricing_el = $('[data-type="pricing-toggler"]');
            pricing_el.change(function (e) {
                if (pricing_el.is(':checked')) {
                    $('.price-mo').hide();
                    $('.price-yr').show();
                    $('.achievement-yr').show().removeClass('bounceOutUp').addClass('bounceInDown');
                } else {
                    $('.price-mo').show();
                    $('.price-yr').hide();
                    $('.achievement-yr').removeClass('bounceInDown').addClass('bounceOutUp');
                }
            });

            $('a[href*=#]:not([href=#])').click(function () {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
                    || location.hostname == this.hostname) {

                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $('html,body').animate({
                            scrollTop: target.offset().top
                        }, 1000, function () {
                            collapseable_el.removeClass('show').hide();
                        });
                        return false;
                    }
                }
            });
        })
    },
    components: {
        'spinny' : Spinny
    }
})