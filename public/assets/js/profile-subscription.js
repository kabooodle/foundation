(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#profile_settings',
    methods: {
        subscribeToTrial: function subscribeToTrial(event) {
            var target = event.target;
            var innerHtml = target.innerHTML;
            target.classList.add('disabled');
            target.disabled = true;
            target.innerHTML = target.innerHTML + spinny();

            this.$http.post(sub_endpoint).then(function (response) {
                target.innerHTML = 'Success! One moment... ' + spinny();
                notify({ text: response.body.data.msg, type: 'success' });
                setTimeout(function () {
                    window.location.href = response.body.data.redirect;
                }, 2500);
            }, function (response) {
                var msg = 'An error occurred. Please try again.';
                if (response.body.data.msg) {
                    msg = response.body.data.msg;
                }
                notify({ text: msg });
                target.classList.remove('disabled');
                target.disabled = false;
                target.innerHTML = innerHtml;
            });
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=profile-subscription.js.map
