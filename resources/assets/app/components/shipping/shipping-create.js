

new Vue({
    el: '#shipping_create',
    props: ["claims_endpoint"],
    data : {
        claims : [],
        packaging : packaging_data,
        claim : null,
    },
    mounted: function(){
        const scope = this;
        $(function(){
            let parcelEl = $('select#parcel_el');
            let claimerEl = $('#claimer_select_el');
            parcelEl.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
            claimerEl.select2({
                templateResult: scope.packagingDropdownTemplate,
                templateSelection: scope.packagingSelectedTemplate
            });
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
    methods : {
        packagingDropdownTemplate: function(parcel){
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

            return $('<span><img  width="60" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
        },
        packagingSelectedTemplate: function(parcel){
            if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

            return $('<span><img  width="30" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
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
                    name: this.name+' ('+this.length+'L x '+this.width+'W x '+this.height+'H)'
                })
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
                        value: this.id,
                        date: this.updated_at_human,
                        image: this.inventory_item_object_data.files.length > 0 ? this.inventory_item_object_data.files[0].location : null,
                        name: this.claimer.name+', '+this.inventory_item_object_data.name+' - '+this.inventory_item_object_data.style_size.name+', $'+this.price
                    })
                });
            }

            // Build our optgroup and options
            $.each(data, function(k,v){
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function(){
                    let attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option '+attr+' value="'+this.value+'"  data-date="'+this.date+'" data-image="'+this.image+'" />').html(this.name).appendTo(group);
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
            let elSelectedVal = elSelected.value;
            let elSelectedType = elSelected.getAttribute('data-type');
            if(elSelectedType == 'claimed_item') {
                let claim = this.getClaimById(elSelectedVal);
                let address = claim.claimer.ship_to_address;
                $('[name="to[name]"]').val(claim.claimer.name);
                $('[name="to[email]"]').val(claim.claimer.email);
                if(address) {
                    this.fillRecipientAddress(address);
                }
            } else {
                this.clearRecipientAddress();
            }
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
            return [
                'company',
                'street1',
                'street2',
                'city',
                'state',
                'country',
                'phone',
                'zip'
            ];
        }
    }
});