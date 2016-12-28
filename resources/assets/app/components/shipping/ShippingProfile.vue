<template>
    <div>
        <div v-if="isMerchantPlus">
            <div class="box">
                <div class="box-header">
                    <h2>Shipping Profile Settings</h2>
                </div>
                <div class="box-divider m-a-0"></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-9">
                            <p>Set Kabooodle as default shipping provider.</p>
                            <small class="text-muted">When a claim is accepted, the claim is automatically added to Kabooodle's shipping queue for you.</small>
                        </div>
                        <div class="col-sm-3">
                            <div class="checkbox  pull-right checkbox-slider--b-flat">
                                <label>
                                    <input
                                        type="checkbox"
                                        v-model="usesKabooodleAsShipper"
                                        v-on:change="updateShippingProfile"
                                    ><span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h2>Ship From Addresses</h2>
                    <small>These are the addresses used as the "From" address when using shipping labels and processing shipments as a seller.</small>
                </div>
                <div class="box-divider m-a-0"></div>
                <div class="box-body">
                    <user-address v-for="(address, index) in fromAddresses" :key="address.id"
                        :address=address
                        :id=address.id
                        :primary-id=primaryFromId
                        :update-primary-endpoint=updatePrimaryEndpoint
                        :addresses-endpoint=addressesEndpoint
                        v-on:new-primary=setNewPrimary
                        v-on:remove-address="fromAddresses.splice(index, 1)"
                    ></user-address>
                    <div class="row">
                        <div class="col-sm-12">
                            <div v-show="addingFromAddress">
                                <div class="form-group">

                                </div>
                                <div class="pull-left">
                                    <button @click="addingFromAddress = !addingFromAddress" class="btn white btn-block p-x-md">Cancel</button>
                                </div>
                                <div class="pull-right">
                                    <button @click="saveFromAddress" class="btn primary btn-block p-x-md">Save</button>
                                </div>
                            </div>
                            <div v-show="!addingFromAddress">
                                <div class="pull-left">
                                    <button @click="addingFromAddress = !addingFromAddress" class="btn white btn-block p-x-md">Add Address</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h2>Ship To Addresses</h2>
                <small>As a buyer, these are the shipping addresses used for the items you purchase.</small>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">
                <user-address v-for="(address, index) in toAddresses" :key="address.id"
                    :address=address
                    :id=address.id
                    :primary-id=primaryToId
                    :update-primary-endpoint=updatePrimaryEndpoint
                    :addresses-endpoint=addressesEndpoint
                    v-on:new-primary=setNewPrimary
                    v-on:remove-address="toAddresses.splice(index, 1)"
                ></user-address>
                <div class="row">
                    <div class="col-sm-12">
                        <div v-show="addingToAddress">
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">Company Name<small class="block text-muted">(Optional)</small></label>
                                <div class="col-sm-6">
                                    <input type="text" v-model="newToCompany" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">Street 1</label>
                                <div class="col-sm-6">
                                    <input type="text" v-model="newToStreet1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">Street 2<small class="block text-muted">(Optional)</small></label>
                                <div class="col-sm-6">
                                    <input type="text" v-model="newToStreet2" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">City</label>
                                <div class="col-sm-6">
                                    <input type="text" v-model="newToCity" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">State</label>
                                <div class="col-sm-3">
                                    <input type="text" v-model="newToState" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">Zip</label>
                                <div class="col-sm-3">
                                    <input type="text" v-model="newToZip" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="form-control-label col-sm-3">Phone <small class="block text-muted">(Optional)</small></label>
                                <div class="col-md-4">
                                    <input type="text" v-model="newToPhone" class="form-control">
                                </div>
                            </div>
                            <div class="pull-left">
                                <button @click="addingToAddress = !addingToAddress" class="btn white btn-block p-x-md">Cancel</button>
                            </div>
                            <div class="pull-right">
                                <button @click="saveToAddress" class="btn primary btn-block p-x-md">Save</button>
                            </div>
                        </div>
                        <div v-show="!addingToAddress">
                            <div class="pull-left">
                                <button @click="addingToAddress = !addingToAddress" class="btn white btn-block p-x-md">Add Address</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import Address from '../address/Address.vue'
    export default {
        props: {
            isMerchantPlus: {
                type: Boolean,
                default: false,
            },
            initialUsesKabooodleAsShipper: {
                type: Boolean,
                required: true,
            },
            shippingProfileUpdateEndpoint: {
                type: String,
                required: true,
            },
            addressesEndpoint: {
                type: String,
                required: true,
            },
            updatePrimaryEndpoint: {
                type: String,
                required: true,
            },
            initialFromAddresses: Array,
            initialPrimaryFromId: {
                type: Number,
                required: true,
            },
            initialToAddresses: Array,
            initialPrimaryToId: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                usesKabooodleAsShipper: this.initialUsesKabooodleAsShipper,
                fromAddresses: this.initialFromAddresses,
                primaryFromId: this.initialPrimaryFromId,
                toAddresses: this.initialToAddresses,
                primaryToId: this.initialPrimaryToId,
                addingFromAddress: false,
                addingToAddress: false,
                newAddressData: null,
                shipFromType: 'ship_from',
                newFromCompany: null,
                newFromStreet1: null,
                newFromStreet2: null,
                newFromCity: null,
                newFromState: null,
                newFromZip: null,
                newFromPhone: null,
                shipToType: 'ship_to',
                newToCompany: null,
                newToStreet1: null,
                newToStreet2: null,
                newToCity: null,
                newToState: null,
                newToZip: null,
                newToPhone: null,
            }
        },
        components:{
            'user-address': Address,
        },
        computed: {
            updateShippingProfileData: function () {
                return {
                    'use_kabooodle_as_shipper': this.usesKabooodleAsShipper ? 1 : 0,
                }
            },
            newFromAddressData: function () {
                return {
                    'type': this.shipFromType,
                    'company': this.newFromCompany,
                    'street1': this.newFromStreet1,
                    'street2': this.newFromStreet2,
                    'city': this.newFromCity,
                    'state': this.newFromState,
                    'zip': this.newFromZip,
                    'phone': this.newFromPhone,
                }
            },
            newToAddressData: function () {
                return {
                    'type': this.shipToType,
                    'company': this.newToCompany,
                    'street1': this.newToStreet1,
                    'street2': this.newToStreet2,
                    'city': this.newToCity,
                    'state': this.newToState,
                    'zip': this.newToZip,
                    'phone': this.newToPhone,
                }
            },
        },
        methods: {
            updateShippingProfile: function () {
                this.$http.put(this.shippingProfileUpdateEndpoint, this.updateShippingProfileData)
                    .then(function (response) {
                        notify({
                            'text': 'Your shipping profile has been updated!',
                            'type': 'success'
                        });
                    }, function (response) {
                        this.usesKabooodleAsShipper = !this.usesKabooodleAsShipper;
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
            },
            setNewPrimary: function (id) {
                this.primaryId = id;
            },
            saveFromAddress: function () {
                this.newAddressData = this.newFromAddressData;
                this.saveAddress();
            },
            saveToAddress: function () {
                this.newAddressData = this.newToAddressData;
                this.saveAddress();
            },
            saveAddress: function () {
                this.$http.post(this.addressesEndpoint, this.newAddressData)
                    .then(function (response) {
                        switch (this.newAddressData.type) {
                            case this.shipFromType:
                                this.fromAddresses.push(response.data.data.address)
                                this.addingFromAddress = false;
                                this.clearNewAddressData(this.shipFromType);
                                break;
                            case this.shipToType:
                                this.toAddresses.push(response.data.data.address)
                                this.addingToAddress = false;
                                this.clearNewAddressData(this.shipToType);
                                break;
                        }
                        notify({
                            'text': 'Your new address has been saved!',
                            'type': 'success'
                        });
                    }, function (response) {
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
            },
            clearNewAddressData: function (type) {
                if (type == this.shipFromType) {
                    this.newFromCompany = null;
                    this.newFromStreet1 = null;
                    this.newFromStreet2 = null;
                    this.newFromCity = null;
                    this.newFromState = null;
                    this.newFromZip = null;
                    this.newFromPhone = null;
                } else if (type == this.shipToType) {
                    this.newToCompany = null;
                    this.newToStreet1 = null;
                    this.newToStreet2 = null;
                    this.newToCity = null;
                    this.newToState = null;
                    this.newToZip = null;
                    this.newToPhone = null;
                }
            },
        },
        created: function () {
        },
    }
</script>
