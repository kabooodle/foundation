

new Vue({
    el: '#shipping_create',
    props: ["claims_endpoint"],
    data : {
        claims : [],
        packaging : packaging_data,
        selectedClaimer : null,
        selectedClaims : [],
        buyerAddress : [],
        defaultAddressKeys : [
        'company',
        'street1',
        'street2',
        'city',
        'state',
        'country',
        'phone',
        'zip'
    ]
    },
    mounted: function(){
        const scope = this;
        $(function(){

            // Handle dynamically added selected claim delete click
            $(document).on('click', '.del-claim', function(event){
                scope.unselectClaimedReference(event);
            });

            scope.setClaimerEl();
            scope.setPackagingEl();

            let parcelEl = $('select#parcel_el');
            let claimerEl = $('select#claimer_select_el');

            claimerEl.on('select2:select', function(event){
                scope.claimReferenceChanged(event);
            });
            parcelEl.on('select2:select', function (event) {
                scope.packagingChanged(event);
            });
        });
    },
    created: function(){
        this.getClaims();
        this.populatePackages();
    },
    watch : {
        selectedClaims : {
            handler : function(v){
                // when selected claims reaches 0, clear the address.
                if (v.length ==0 ){
                    this.clearRecipientAddress();
                }
            }
        }
    },
    methods : {
        //
        unselectClaimedReference : function(event){
            $(event.target).closest('.select2-selection__rendered').remove();
            this.selectedClaims.splice(this.selectedClaims.indexOf($(event.target).data('claim-id')));
            $('select#claimer_select_el option[value="'+$(event.target).data('claim-id')+'"]').prop('disabled', false);
            this.setClaimerEl().val('').trigger('change');
        },
        setClaimerEl : function(){
            const scope = this;
            let el = $('select#claimer_select_el');
            return el.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
        },
        setPackagingEl: function(){
            const scope = this;
            let el = $('select#parcel_el');
            el.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
        },
        packagingDropdownTemplate: function(parcel){
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

            return $('<span><img  width="40" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
        },
        packagingSelectedTemplate: function(parcel){
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

            return $('<span><img  width="20" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
        },
        populatePackages: function(){
            let el = $('#parcel_el');
            let packagings = this.packaging;

            // Set our default array.
            let data = {
                USPS : []
            };

            $.each(packagings, function(k,v){
                data.USPS.push({
                    value: this.parcel_id,
                    image: this.image,
                    name: this.name_with_dimensions
                });
            });

            // Build our optgroup and options
            $.each(data, function(k,v){
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function(){
                    let attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option '+attr+' value="'+this.value+'"  data-image="'+this.image+'"  />').html(this.name).appendTo(group);
                });
                group.appendTo(el);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: 'self',  text: 'Define your own packaging', 'data-image': 'http://img.thrfun.com/img/077/838/lost_package_l1.jpg', selected: true }).prependTo(el);
            el.removeClass('disabled').prop('disabled', false);
        },
        getClaims: function(){
            const scope = this;
            this.$http.get(claims_endpoint).then(function(response){
                return JSON.parse(response.body.data);
            }, function(){
                return [];
            }).then(function(response){
                scope.claims = response;
                scope.populateClaims();
            });
        },
        populateClaims: function(){
            let claimedEl = $('#claimer_select_el');
            let claims = this.claims;
            // Set our default array.
            let data = {
                Claims : []
            };

            // If we have claims, iterate over them and push the claim to the data.Claims array.
            if(claims.length > 0) {
                $.each(claims, function(k,v){
                    data.Claims.push({
                        value: parseInt(this.id),
                        claimer_id: this.claimer.id,
                        date: this.updated_at_human,
                        image: this.inventory_item_object_data.files && this.inventory_item_object_data.files.length > 0 ? this.inventory_item_object_data.files[0].location : null,
                        name: this.claimer.name+', '+this.inventory_item_object_data.name+' - '+this.inventory_item_object_data.style_size.name+', $'+this.price
                    });
                });
            }

            // Build our optgroup and options
            $.each(data, function(k,v){
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function(){
                    let typeAttr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    let claimerAttr = k == 'Claims' ? ' data-claimer-id="'+this.claimer_id+'" ' : '';
                    $('<option ' +
                        ''+typeAttr+'' +
                        ''+claimerAttr+''+
                        'value="'+this.value+'"  ' +
                        'data-date="'+this.date+'" ' +
                        'data-image="'+this.image+'" />').html(this.name).appendTo(group);
                });
                group.appendTo(claimedEl);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: '',  text: 'None - Manual Entry', selected: true }).prependTo(claimedEl);
            claimedEl.removeClass('disabled').prop('disabled', false);
        },
        claimReferenceChanged : function(event){
            let el = event.target;
            let elSelected = el.options[el.selectedIndex];
            let elSelectedVal = parseInt(elSelected.value);
            let elSelectedType = elSelected.getAttribute('data-type');
            let elSelectedClaimerId = elSelected.getAttribute('data-claimer-id');

            // Set address
            if(elSelectedType == 'claimed_item') {
                let claim = this.getClaimById(elSelectedVal);
                let address = claim.claimer.ship_to_address;
                $('[name="to[name]"]').val(claim.claimer.name);
                $('[name="to[email]"]').val(claim.claimer.email);
                if(address) {
                    this.address = address;
                    this.fillRecipientAddress(address);
                }
            } else {
                this.clearRecipientAddress();
            }

            // Check claimer id
            if(elSelectedClaimerId) {
                let selectedClaim = _.findWhere(this.claims, {id: parseInt(elSelectedVal)});
                //Check if the claimer id is the same as the one already selected
                if(this.selectedClaimer != elSelectedClaimerId){
                    // Reset the claims and claimer
                    $('#claimed_items_container').html('');
                    $('select#claimer_select_el option').prop('disabled', false);
                    this.selectedClaimer = elSelectedClaimerId;
                    this.selectedClaims = [];
                }

                this.selectedClaims.push(selectedClaim);
                let $html = $($('#select2-claimer_select_el-container').parent().html());
                $html.find('span').parent().addClass('clearfix').append($('<input type="hidden" name="claim_id[]" value="'+elSelectedVal+'"> <i data-claim-id="'+elSelectedVal+'" class="fa fa-times text-muted pull-right del-claim"></i>'));
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
        getClaimById : function(id) {
            return _.find(this.claims, function(claim){ return claim.id == id});
        },
        packagingChanged : function(event){
            let parcelEl = event.target;
            let parcelElParent = $('#packaging-self-wrapper');
            if (parcelEl.options[parcelEl.selectedIndex].value == 'self') {
                parcelElParent.show().find(':input').prop('disabled', false);
            } else {
                parcelElParent.hide().find(':input').not('select').prop('disabled', true).val('');
            }
        },
        fillRecipientAddress: function(address){
            $.each(this.getAddressKeys(), function(k,v){
                if(v in address) {
                    $('[name="to['+v+']"]').val(address[v]);
                }
            });
        },
        clearRecipientAddress: function(){
            $('[name="to[name]"]').val('');
            $('[name="to[email]"]').val('');
            $.each(this.getAddressKeys(), function(k,v){
                $('[name="to['+v+']"]').val('');
            });
        },
        getAddressKeys: function(){
            return this.defaultAddressKeys;
        }
    }
});