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
        </div>
        <div v-else>
            <p>You gotta get on the merchant plus shit!</p>
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
                addingAddress: false,
                newAddress: null,
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
            newAddressData: function () {
                return {
                    'company': this.company,
                    'street1': this.street1,
                    'street2': this.street2,
                    'city': this.city,
                    'state': this.state,
                    'zip': this.zip,
                    'phone': this.phone,
                }
            }
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
            saveAddress: function () {
                this.$http.post(this.newAddressEndpoint, this.newAddressData)
                    .then(function (response) {
                        switch (response.data.data.type) {
                            case 'ship_from':
                                this.fromAddresses.push(response.data.data.address)
                                break;
                            case 'ship_to':
                                this.toAddresses.push(response.data.data.address)
                                break;
                        }
                        this.addingAddress = false;
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
        },
    }
</script>
