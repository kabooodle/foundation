(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

new Vue({
    el: '#shipping_create',
    props: ["claims_endpoint"],
    data: {
        claims: [],
        packaging: packaging_data,
        selectedClaimer: null,
        selectedClaims: [],
        buyerAddress: [],
        defaultAddressKeys: ['company', 'street1', 'street2', 'city', 'state', 'country', 'phone', 'zip']
    },
    mounted: function mounted() {
        var scope = this;
        $(function () {

            // Handle dynamically added selected claim delete click
            $(document).on('click', '.del-claim', function (event) {
                scope.unselectClaimedReference(event);
            });

            scope.setClaimerEl();
            scope.setPackagingEl();

            var parcelEl = $('select#parcel_el');
            var claimerEl = $('select#claimer_select_el');

            claimerEl.on('select2:select', function (event) {
                scope.claimReferenceChanged(event);
            });
            parcelEl.on('select2:select', function (event) {
                scope.packagingChanged(event);
            });
        });
    },
    created: function created() {
        this.getClaims();
        this.populatePackages();
    },
    watch: {
        selectedClaims: {
            handler: function handler(v) {
                // when selected claims reaches 0, clear the address.
                if (v.length == 0) {
                    this.clearRecipientAddress();
                }
            }
        }
    },
    methods: {
        //
        unselectClaimedReference: function unselectClaimedReference(event) {
            $(event.target).closest('.select2-selection__rendered').remove();
            this.selectedClaims.splice(this.selectedClaims.indexOf($(event.target).data('claim-id')));
            $('select#claimer_select_el option[value="' + $(event.target).data('claim-id') + '"]').prop('disabled', false);
            this.setClaimerEl().val('').trigger('change');
        },
        setClaimerEl: function setClaimerEl() {
            var scope = this;
            var el = $('select#claimer_select_el');
            return el.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
        },
        setPackagingEl: function setPackagingEl() {
            var scope = this;
            var el = $('select#parcel_el');
            el.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
        },
        packagingDropdownTemplate: function packagingDropdownTemplate(parcel) {
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) {
                return parcel.text;
            }

            return $('<span><img  width="40" src="' + parcel.element.getAttribute('data-image') + '"  /> ' + parcel.text + '</span>');
        },
        packagingSelectedTemplate: function packagingSelectedTemplate(parcel) {
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) {
                return parcel.text;
            }

            return $('<span><img  width="20" src="' + parcel.element.getAttribute('data-image') + '"  /> ' + parcel.text + '</span>');
        },
        populatePackages: function populatePackages() {
            var el = $('#parcel_el');
            var packagings = this.packaging;

            // Set our default array.
            var data = {
                USPS: []
            };

            $.each(packagings, function (k, v) {
                data.USPS.push({
                    value: this.parcel_id,
                    image: this.image,
                    name: this.name_with_dimensions
                });
            });

            // Build our optgroup and options
            $.each(data, function (k, v) {
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function () {
                    var attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option ' + attr + ' value="' + this.value + '"  data-image="' + this.image + '"  />').html(this.name).appendTo(group);
                });
                group.appendTo(el);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: 'self', text: 'Define your own packaging', 'data-image': 'http://img.thrfun.com/img/077/838/lost_package_l1.jpg', selected: true }).prependTo(el);
            el.removeClass('disabled').prop('disabled', false);
        },
        getClaims: function getClaims() {
            var scope = this;
            this.$http.get(claims_endpoint).then(function (response) {
                return response.body.data;
            }, function () {
                return [];
            }).then(function (response) {
                scope.claims = response;
                scope.populateClaims();
            });
        },
        populateClaims: function populateClaims() {
            var claimedEl = $('#claimer_select_el');
            var claims = this.claims;
            // Set our default array.
            var data = {
                Claims: []
            };

            // If we have claims, iterate over them and push the claim to the data.Claims array.
            if (claims.length > 0) {
                $.each(claims, function (k, v) {
                    data.Claims.push({
                        value: parseInt(this.id),
                        claimer_id: this.claimer.id,
                        date: this.updated_at_human,
                        image: this.inventory_item_object_data.files && this.inventory_item_object_data.files.length > 0 ? this.inventory_item_object_data.files[0].location : null,
                        name: this.claimer.name + ', ' + this.inventory_item_object_data.name + ' - ' + this.inventory_item_object_data.style_size.name + ', $' + this.price
                    });
                });
            }

            // Build our optgroup and options
            $.each(data, function (k, v) {
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function () {
                    var typeAttr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    var claimerAttr = k == 'Claims' ? ' data-claimer-id="' + this.claimer_id + '" ' : '';
                    $('<option ' + '' + typeAttr + '' + '' + claimerAttr + '' + 'value="' + this.value + '"  ' + 'data-date="' + this.date + '" ' + 'data-image="' + this.image + '" />').html(this.name).appendTo(group);
                });
                group.appendTo(claimedEl);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: '', text: 'None - Manual Entry', selected: true }).prependTo(claimedEl);
            claimedEl.removeClass('disabled').prop('disabled', false);
        },
        claimReferenceChanged: function claimReferenceChanged(event) {
            var el = event.target;
            var elSelected = el.options[el.selectedIndex];
            var elSelectedVal = parseInt(elSelected.value);
            var elSelectedType = elSelected.getAttribute('data-type');
            var elSelectedClaimerId = elSelected.getAttribute('data-claimer-id');

            // Set address
            if (elSelectedType == 'claimed_item') {
                var claim = this.getClaimById(elSelectedVal);
                var address = claim.claimer.ship_to_address;
                $('[name="to[name]"]').val(claim.claimer.name);
                $('[name="to[email]"]').val(claim.claimer.email);
                if (address) {
                    this.address = address;
                    this.fillRecipientAddress(address);
                }
            } else {
                this.clearRecipientAddress();
            }

            // Check claimer id
            if (elSelectedClaimerId) {
                var selectedClaim = _.findWhere(this.claims, { id: parseInt(elSelectedVal) });
                //Check if the claimer id is the same as the one already selected
                if (this.selectedClaimer != elSelectedClaimerId) {
                    // Reset the claims and claimer
                    $('#claimed_items_container').html('');
                    $('select#claimer_select_el option').prop('disabled', false);
                    this.selectedClaimer = elSelectedClaimerId;
                    this.selectedClaims = [];
                }

                this.selectedClaims.push(selectedClaim);
                var $html = $($('#select2-claimer_select_el-container').parent().html());
                $html.find('span').parent().addClass('clearfix').append($('<input type="hidden" name="claim_id[]" value="' + elSelectedVal + '"> <i data-claim-id="' + elSelectedVal + '" class="fa fa-times text-muted pull-right del-claim"></i>'));
                $html.appendTo($('#claimed_items_container'));

                $('select#claimer_select_el option:selected').prop('disabled', true);
                this.setClaimerEl().val('').trigger('change');

                return;
            }

            // Reset everything;
            this.selectedClaimer = null;
            this.selectedClaims = [];
            $('#claimed_items_container').html('');
            $('select#claimer_select_el option').prop('disabled', false);
            this.setClaimerEl().val('').trigger('change');
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
            return this.defaultAddressKeys;
        }
    }
});

},{}]},{},[1]);

//# sourceMappingURL=shipping-create.js.map
