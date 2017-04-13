<template>
    <div class="row">
        <div class="col-md-12">
            <form id="shipping-form" @submit.prevent>
                <div class="box white">
                    <div class="box-header">
                        <h3>
                            Claimed Item Information
                        </h3>
                    </div>
                    <div class="box-divider"></div>
                    <div class="box-body">
                        <inline-field :errors="form_errors" id="packaging-wrapper" class="form-group row" field_name="claim_id">
                            <template slot="label">Approved Claim(s)</template>
                            <template slot="input">
                                <div class="col-sm-8">
                                    <select disabled @change="claimReferenceChanged" name="claim_id[]" class="pull-left disabled form-control" id="claimer_select_el"></select>
                                    <div id="claimed_items_container"></div>
                                    <span class="text-xs text-muted">Optionally ship multiple claims, from the same recipient, at once.</span>
                                </div>
                                <div class="col-sm-1">
                                    <spinny v-if="events.fetching_claims"></spinny>
                                </div>
                            </template>
                        </inline-field>
                    </div>
                </div>

                <div class="box white">
                    <div class="box-header">
                        <h3>Parcel Information</h3>
                    </div>
                    <div class="box-divider"></div>
                    <div class="box-body">

                        <inline-field :errors="form_errors" id="packaging-wrapper" class="form-group row" field_name="parcel.id">
                            <template slot="label">Packaging</template>
                            <template slot="input">
                                <div class="col-sm-8">
                                    <select
                                            :disabled="events.fetching_packaging"
                                            @change="packagingChanged"
                                            name="parcel[id]"
                                            class="form-control"
                                            :class="events.fetching_packaging ? 'disabled' : null"
                                            data-size="auto"
                                            data-width="100%"
                                            id="parcel_el"></select>
                                </div>
                                <div class="col-sm-1">
                                    <spinny v-if="events.fetching_packaging"></spinny>
                                </div>
                            </template>
                        </inline-field>

                        <div id="packaging-self-wrapper">

                            <inline-field :errors="form_errors" class="form-group row" field_name="parcel.length">
                                <template slot="label">Length</template>
                                <template slot="input">
                                    <div class="col-sm-3">
                                        <input type="number" name="parcel[length]" class="form-control numeric float" numeric min="0" length="444">
                                    </div>
                                </template>
                            </inline-field>

                            <inline-field :errors="form_errors" class="form-group row" field_name="parcel.width">
                                <template slot="label">Width</template>
                                <template slot="input">
                                    <div class="col-sm-3">
                                        <input type="number" name="parcel[width]" class="form-control numeric float" numeric  min="0" length="444">
                                    </div>
                                </template>
                            </inline-field>

                            <inline-field :errors="form_errors" class="form-group row" field_name="parcel.height">
                                <template slot="label">Height</template>
                                <template slot="input">
                                    <div class="col-sm-3">
                                        <input type="number" name="parcel[height]" class="form-control numeric float" numeric  min="0" length="444">
                                    </div>
                                </template>
                            </inline-field>

                            <inline-field :errors="form_errors" class="form-group row" field_name="parcel.distance_unit">
                                <template slot="label">Units</template>
                                <template slot="input">
                                    <div class="col-sm-2">
                                    <select name="parcel[distance_unit]" class="form-control">
                                        <option v-for="unit in parcel_units" :value="unit">{{ unit }}</option>
                                    </select>
                                    </div>
                                </template>
                            </inline-field>
                            <hr>
                        </div>

                        <inline-field :errors="form_errors" class="form-group row" field_name="parcel.weight">
                            <template slot="label">Weight</template>
                            <template slot="input">
                                <div class="col-sm-3">
                                <input type="number" name="parcel[weight]" class="form-control numeric float"  min="0" numeric max="999">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="parcel[weight_uom]">
                                        <option v-for="weight in parcel_weights" :value="weight" :selected="weight == 'oz'">{{ weight }}</option>
                                    </select>
                                </div>
                            </template>
                        </inline-field>

                    </div>
                </div>

                <div class="box white">
                    <div class="box-header">
                        <h3>Recipient Address</h3>
                    </div>
                    <div class="box-divider"></div>
                    <div class="box-body">

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.name">
                            <template slot="label">Recipient Name</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input type="text" name="to[name]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.company">
                            <template slot="label">Company Name</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="to[company]" class="form-control">
                                    <small class="text-xs text-muted">(optional)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.street1">
                            <template slot="label">Street 1</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="to[street1]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.street2">
                            <template slot="label">Street 2</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="to[street2]" class="form-control">
                                    <small class="text-xs text-muted">(optional)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.city">
                            <template slot="label">City</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="to[city]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.state">
                            <template slot="label">State</template>
                            <template slot="input">
                                <div class="col-sm-2">
                                    <select name="to[state]" class="form-control">
                                        <option v-for="(state, $index) in states" :value="$index">{{ state }}</option>
                                    </select>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.zip">
                            <template slot="label">Zip</template>
                            <template slot="input">
                                <div class="col-sm-3">
                                    <input name="to[zip]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.phone">
                            <template slot="label">Phone</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input name="to[phone]" class="form-control">
                                    <small class="text-xs text-muted">(USPS requirement)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="to.email">
                            <template slot="label">Email</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input name="to[email]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                    </div>
                </div>



                

                <div class="box white">
                    <div class="box-header">
                        <h3>Sender Address</h3>
                        <p class="text-muted m-b-0">This address is used on the return label.</p>
                    </div>
                    <div class="box-divider"></div>
                    <div class="box-body">
                        <!--<inline-field :errors="form_errors" class="form-group row" >-->
                            <!--<template slot="label">Pre-saved Addresses</template>-->
                            <!--<template slot="input">-->
                                <!--<div class="col-sm-4">-->
                                    <!--<select class="form-control">-->
                                        <!--<option></option>-->
                                    <!--</select>-->
                                <!--</div>-->
                            <!--</template>-->
                        <!--</inline-field>-->

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.name">
                            <template slot="label">Sender Name</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input name="from[name]" class="form-control" :value="currentUser.full_name">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.company">
                            <template slot="label">Company Name</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="from[company]" class="form-control">
                                    <small class="text-xs text-muted">(optional)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.street1">
                            <template slot="label">Street 1</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="from[street1]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.street2">
                            <template slot="label">Street 2</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="from[street2]" class="form-control">
                                    <small class="text-xs text-muted">(optional)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.city">
                            <template slot="label">City</template>
                            <template slot="input">
                                <div class="col-sm-6">
                                    <input type="text" name="from[city]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.state">
                            <template slot="label">State</template>
                            <template slot="input">
                                <div class="col-sm-2">
                                    <select name="from[state]" class="form-control">
                                        <option v-for="(state, $index) in states" :value="$index">{{ state }}</option>
                                    </select>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.zip">
                            <template slot="label">Zip</template>
                            <template slot="input">
                                <div class="col-sm-3">
                                    <input name="from[zip]" class="form-control">
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.phone">
                            <template slot="label">Phone</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input name="from[phone]" class="form-control" >
                                    <small class="text-xs text-muted">(USPS requirement)</small>
                                </div>
                            </template>
                        </inline-field>

                        <inline-field :errors="form_errors" class="form-group row" field_name="from.email">
                            <template slot="label">Email</template>
                            <template slot="input">
                                <div class="col-sm-4">
                                    <input name="from[email]" class="form-control" :value="currentUser.primary_email.address">
                                </div>
                            </template>
                        </inline-field>

                    </div>
                </div>

                <div class="form-group row m-b-0">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button
                                :class="events.saving ? 'disabled' : null"
                                :disabled="events.saving"
                                type="button"
                                class="btn primary"
                                @click.prevent="submitParcelForm"
                        >Continue to pricing
                            <spinny v-if="events.saving"></spinny>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import InlineField from '../InlineFieldCustom.vue';
    import currentUser from '../current-user';
    import Multiselect from 'vue-multiselect';

    export default{
        props: {
            parcel_units: {
                required: true,
                type: Object,
            },
            parcel_weights: {
                required: true,
                type: Object,
            },
            packaging_data: {
                required: true,
                type: Array,
            },
            endpoint_submit_parcel: {
                required: true,
                type: String
            },
            endpoint_claims: {
                required: true,
                type: String
            },
            states: {
                required: true,
                type: Object
            },
            sender_address: {
                type: Object,
                required: true
            }
        },
        data(){
            return{
                currentUser: currentUser(),
                form_errors: [],
                events: {
                    saving: false,
                    mounted: false,
                    created: false,
                    fetching_claims: false,
                    fetching_packaging: false,
                },
                claims : [],
                packaging : this.packaging_data,
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
            }
        },
        mounted: function(){
            this.events.mounted = true;
            const scope = this;
            this.$nextTick(()=>{
                // Handle dynamically added selected claim delete click
                $(document).on('click', '.del-claim', function(event){
                    scope.unselectClaimedReference(event);
                });

                let parcelEl = $('select#parcel_el');
                let claimerEl = $('select#claimer_select_el');

                scope.setClaimerEl();
                scope.setPackagingEl();
                scope.fillSenderAddress();

                claimerEl.on('select2:select', function(event){
                    scope.claimReferenceChanged(event);
                });
                parcelEl.on('select2:select', function (event) {
                    scope.packagingChanged(event);
                });
            });
        },
        created: function(){
            this.$nextTick(()=>{
                this.getClaims();
                this.populatePackages();
            });
        },
        watch : {
            selectedClaims : {
                handler : function(v){
                    // when selected claims reaches 0, clear the address.
                    if (v.length == 0 ){
                        this.clearRecipientAddress();
                    }
                }
            }
        },
        methods : {
            /**
             *
             * @param event
             */
            unselectClaimedReference : function(event){
                $(event.target).closest('.select2-selection__rendered').remove();
                this.selectedClaims.splice(this.selectedClaims.indexOf($(event.target).data('claim-id')));
                $('select#claimer_select_el option[value="'+$(event.target).data('claim-id')+'"]').prop('disabled', false);
                this.setClaimerEl().val('').trigger('change');
            },

            /**
             *
             * @returns {*}
             */
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

            /**
             *
             * @param parcel
             * @returns {*}
             */
            packagingDropdownTemplate: function(parcel){
                if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

                return $('<span><img  width="60" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
            },

            /**
             *
             * @param parcel
             * @returns {*}
             */
            packagingSelectedTemplate: function(parcel){
                if (!parcel.id || parcel.element.getAttribute('data-image') == null || parcel.element.getAttribute('data-image') == undefined) { return parcel.text; }

                return $('<span><img  width="20" src="' + parcel.element.getAttribute('data-image')+'"  /> ' + parcel.text + '</span>');
            },

            populatePackages: function(){
                this.events.fetching_packaging = true;
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

                this.events.fetching_packaging = false;
            },

            getClaims: function(){
                const scope = this;
                this.events.fetching_claims = true;
                this.$http.get(this.endpoint_claims).then(function(response){
                    return (response.body.data);
                }, function(){
                    return [];
                }).then(function(response){
                    scope.claims = response;
                    scope.populateClaims();
                    this.events.fetching_claims = false;
                });
            },

            populateClaims: function(){
                const scope = this;
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
                            claimer_id: this.user_id,
                            date: this.claim_created_at,
                            image: this.listable_cover_photo_location,
                            name: this.username+', '+this.name_alt+', '+this.price
                        });
                    });
                }

                // Build our optgroup and options
                $.each(data, function(k,v){
                    // var group = $('<optgroup label="' + k + '" />');
                    let preselected = /[?&]c=/.test(location.search);


                    $.each(v, function(){
                        let typeAttr = k == 'Claims' ? ' data-type="claimed_item" ' : '';
                        let claimerAttr = k == 'Claims' ? ' data-claimer-id="'+this.claimer_id+'" ' : '';
                        let selected = false;
                        if(preselected && this.value == getAllUrlParams().c) {
                            selected = 'selected="selected"';
                        }
                        $('<option ' +
                                ' '+typeAttr+' ' +
                                ' '+claimerAttr+' '+
                                ' '+selected+' '+
                                'value="'+this.value+'"  ' +
                                'data-date="'+this.date+'" ' +
                                'data-image="'+this.image+'">').html(this.name).prependTo(claimedEl);
                    });

                    if(preselected && getAllUrlParams().c) {
                        scope.setClaimerEl().trigger('select2:select');
                    }

                });

                // We need an empty option, push it to the front.
                // $("<option>", { value: '',  text: 'None - Manual Entry', selected: true }).prependTo(claimedEl);
                claimedEl.removeClass('disabled').prop('disabled', false);
                claimedEl.parent().find('.spinny').hide();
            },

            /**
             *
             * @param event
             */
            claimReferenceChanged : function(event){
                let el = event.target;
                let elSelected = el.options[el.selectedIndex];
                let elSelectedVal = parseInt(elSelected.value);
                let elSelectedType = elSelected.getAttribute('data-type');
                let elSelectedClaimerId = elSelected.getAttribute('data-claimer-id');

                // Set address
                if(elSelectedType == 'claimed_item') {
                    let claim = this.getClaimById(elSelectedVal);
                    let address = claim.shipping_address;
                    $('[name="to[name]"]').val(claim.full_name);
                    $('[name="to[email]"]').val(claim.email);
                    if(address) {
                        this.address = address;
                        this.fillRecipientAddress(address);
                    }
                } else {
                    this.clearRecipientAddress();
                }

                // Check claimer id
                if(elSelectedClaimerId) {
                    let selectedClaim = _.find(this.claims, {id: parseInt(elSelectedVal)});
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

            /**
             *
             * @param Int id
             */
            getClaimById : function(id) {
                return _.find(this.claims, function(claim){ return claim.id == id});
            },

            /**
             *
             * @param event
             */
            packagingChanged : function(event){
                let parcelEl = event.target;
                let parcelElParent = $('#packaging-self-wrapper');
                if (parcelEl.options[parcelEl.selectedIndex].value == 'self') {
                    parcelElParent.show().find(':input').prop('disabled', false);
                } else {
                    parcelElParent.hide().find(':input').not('select').prop('disabled', true).val('');
                }
            },

            /**
             *
             * @param address
             */
            fillRecipientAddress: function(address){
                $.each(this.getAddressKeys(), function(k,v){
                    if(v in address) {
                        $('[name="to['+v+']"]').val(address[v]);
                    }
                });
            },

            fillSenderAddress: function(){
                $.each(this.getAddressKeys(), (k,v)=>{
                    if (v in this.sender_address) {
                        $('[name="from['+v+']"]').val(this.sender_address[v]);
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
            },

            /**
             *
             * @param String url
             */
            submitParcelForm(){
                this.events.saving = true;
                this.form_errors = [];
                const form_data = $('#shipping-form').serializeObject();

                this.$http.post(this.endpoint_submit_parcel, form_data).then((response)=>{
                    const data = response.body.data;
                    $Bus.$emit('parcel:saved', form_data, data.rates, data.shipment);
                }, (response)=>{
                    const data = response.body.data;
                    if (data.hasOwnProperty('errors')) {
                        this.form_errors = data.errors;
                    }
                    notify({
                        text: data.msg
                    });
                }).finally(()=>{
                    this.events.saving = false;
                });
            },
        },
        components: {
            'spinny' : Spinny,
            'inline-field' : InlineField,
            'multiselect' : Multiselect
        }
    }
</script>
