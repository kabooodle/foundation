(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#profile_settings',
    methods: {
        changed: function changed(event) {
            var notification_id = event.target.getAttribute('data-id');
            var type = event.target.getAttribute('data-type');
            var action = event.target.checked ? 'subscribed' : 'unsubscribed';
            var data = {
                'id': notification_id,
                'action': action,
                'type': type
            };
            event.target.classList.add("disabled");
            event.target.disabled = true;
            this.$http.post(notifications_route, data).then(function (response) {}, function (response) {
                alert('An error occurred, please try again');
                event.target.checked = action != 'subscribed';
            }).finally(function () {
                event.target.classList.remove("disabled");
                event.target.disabled = false;
            });
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=profile-notifications.js.map
