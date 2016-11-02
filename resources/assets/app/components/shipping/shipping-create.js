

new Vue({
    el: '#shipping_create',
    data : {
        claims : claims,
        claim : null,
    },
    created: function(){
        this.populateClaims();
    },
    methods : {
        populateClaims: function(){
            let claimedEl = $('#claimer_select_el');
            let claims = this.claims;

            // Set our default array.
            let data = {
                Claims : [],
                Custom: [
                    {
                        name: 'Other',
                        value: 'other',
                    }
                ],
            };

            // If we have claims, iterate over them and push the claim to the data.Claims array.
            if(claims.length > 0) {
                $.each(claims, function(k,v){
                    data.Claims.push({
                        value: this.id,
                        name: this.claimer.name+', '+this.inventory_item_object_data.name+', $'+this.price+', '+this.updated_at_human
                    })
                });
            }

            // Build our optgroup and options
            $.each(data, function(k,v){
                var group = $('<optgroup label="' + k + '" />');
                $.each(v, function(){
                    let attr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                    $('<option '+attr+' value="'+this.value+'"/>').html(this.name).appendTo(group);
                });
                group.appendTo(claimedEl);
            });

            // We need an empty option, push it to the front.
            $("<option>", { value: '', selected: true }).prependTo(claimedEl);
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