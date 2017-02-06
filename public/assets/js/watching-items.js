(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#watching_items',

    methods: {
        deleteWatching: function deleteWatching(event) {
            event.preventDefault();
            var form = event.target;
            var submitEl = form.getElementsByClassName('btn-action-del')[0];
            var endpoint = form.getAttribute('action');
            var container = form.closest('tr');

            submitEl.classList.add('disabled');
            submitEl.disabled = true;
            submitEl.innerHTML = submitEl.innerHTML + spinny();

            this.$http.delete(endpoint).then(function (response) {
                return container.parentNode.removeChild(container);
            });
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=watching-items.js.map
