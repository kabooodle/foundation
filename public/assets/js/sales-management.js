(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#sales_index',
    mounted: function mounted() {
        $(document).on('click', '[data-toggle="toggleShippingMethod"]', this.toggleShippingMethod);
    },
    methods: {
        toggleShippingMethod: function toggleShippingMethod(event) {
            var $that = $(event.target);
            var url = event.target.dataset.route;
            var method = event.target.dataset.method;

            $that.html($that.html() + spinny());

            $that.closest('tr').addClass('disabled text-muted').prop('disabled', true).find('button, input, .btn').addClass('disabled').prop('disabled', true);

            this.$http.post(url, { method: method }).then(function (response) {
                $that.closest('tr').html(response.body.data).removeClass('disabled text-muted').prop('disabled', false).find('button, input, .btn').removeClass('disabled').prop('disabled', false);
            }, function (response) {}).then(function () {
                $that.closest('tr').removeClass('disabled text-muted').prop('disabled', false).find('button, input, .btn').removeClass('disabled').prop('disabled', false);
            });
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=sales-management.js.map
