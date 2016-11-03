(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#shipping_create',
    props: ["route"],
    data: {
        claims: [],
        claim: null
    },
    mounted: function mounted() {
        $(function () {
            $('select#parcel_el').select2();
        });
    },
    created: function created() {
        this.populateClaims();
    },
    methods: {
        populatePackages: function populatePackages() {
            var claims = this.claims;

            // Set our default array.
            var data = {
                Claims: [],
                Custom: [{
                    name: 'Other',
                    value: 'other'
                }]
            };

            // If we have claims, iterate over them and push the claim to the data.Claims array.
            if (claims.length > 0) {
                $.each(claims, function (k, v) {
                    data.Claims.push({
                        value: this.id,
                        name: this.claimer.name + ', ' + this.inventory_item_object_data.name + ', $' + this.price + ', ' + this.updated_at_human
                    });
                });
            }

            // Build our optgroup and options
            $.each(data, function (k, v) {
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function () {
                    var attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option ' + attr + ' value="' + this.value + '"/>').html(this.name).appendTo(group);
                });
                group.appendTo(claimedEl);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: '', selected: true }).prependTo(claimedEl);
            claimedEl.removeClass('disabled').prop('disabled', false);
        },
        getClaims: function getClaims() {
            this.$http.get(claims_route).then(function (response) {
                return JSON.parse(response.body.data);
            }, function (response) {
                return [];
            });
        },
        populateClaims: function populateClaims() {
            var claimedEl = $('#claimer_select_el');
            var claims = this.claims;

            // Set our default array.
            var data = {
                Claims: [],
                Custom: [{
                    name: 'Other',
                    value: 'other'
                }]
            };

            // If we have claims, iterate over them and push the claim to the data.Claims array.
            if (claims.length > 0) {
                $.each(claims, function (k, v) {
                    data.Claims.push({
                        value: this.id,
                        name: this.claimer.name + ', ' + this.inventory_item_object_data.name + ', $' + this.price + ', ' + this.updated_at_human
                    });
                });
            }

            // Build our optgroup and options
            $.each(data, function (k, v) {
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function () {
                    var attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option ' + attr + ' value="' + this.value + '"/>').html(this.name).appendTo(group);
                });
                group.appendTo(claimedEl);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: '', selected: true }).prependTo(claimedEl);
            claimedEl.removeClass('disabled').prop('disabled', false);
        },
        claimReferenceChanged: function claimReferenceChanged(event) {
            var el = event.target;
            var elSelected = el.options[el.selectedIndex];
            var elSelectedVal = elSelected.value;
            var elSelectedType = elSelected.getAttribute('data-type');
            if (elSelectedType == 'claimed_item') {
                var claim = this.getClaimById(elSelectedVal);
                var address = claim.claimer.ship_to_address;
                $('[name="to[name]"]').val(claim.claimer.name);
                $('[name="to[email]"]').val(claim.claimer.email);
                if (address) {
                    this.fillRecipientAddress(address);
                }
            } else {
                this.clearRecipientAddress();
            }
        },
        getClaimById: function getClaimById(id) {
            return _.find(this.claims, function (claim) {
                return claim.id == id;
            });
        },
        packagingChanged: function packagingChanged(event) {
            var parcelEl = event.target;
            var parcelElParent = $('#packaging-self-wrapper');
            if (parcelEl.options[parcelEl.selectedIndex].value == 'self') {
                parcelElParent.show().find(':input').prop('disabled', false);
            } else {
                parcelElParent.hide().find(':input').not('select').prop('disabled', true).val('');
            }
        },
        fillRecipientAddress: function fillRecipientAddress(address) {
            $.each(this.getAddressKeys(), function (k, v) {
                if (v in address) {
                    $('[name="to[' + v + ']"]').val(address[v]);
                }
            });
        },
        clearRecipientAddress: function clearRecipientAddress() {
            $('[name="to[name]"]').val('');
            $('[name="to[email]"]').val('');
            $.each(this.getAddressKeys(), function (k, v) {
                $('[name="to[' + v + ']"]').val('');
            });
        },
        getAddressKeys: function getAddressKeys() {
            return ['company', 'street1', 'street2', 'city', 'state', 'country', 'phone', 'zip'];
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=shipping-create.js.map
