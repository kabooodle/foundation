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
                    <addresses
                        :type=shipFromType
                        :addresses-endpoint=addressesEndpoint
                        :update-primary-endpoint=updatePrimaryEndpoint
                        :initial-addresses=initialFromAddresses
                        :initial-primary-id=initialPrimaryFromId
                    ></addresses>
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
                <addresses
                    :type=shipToType
                    :addresses-endpoint=addressesEndpoint
                    :update-primary-endpoint=updatePrimaryEndpoint
                    :initial-addresses=initialToAddresses
                    :initial-primary-id=initialPrimaryToId
                ></addresses>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import Addresses from '../address/Addresses.vue'
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
            shipFromType: {
                type: String,
                required: true,
            },
            initialFromAddresses: Array,
            initialPrimaryFromId: {
                type: Number,
                required: true,
            },
            shipToType: {
                type: String,
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
            }
        },
        components:{
            'addresses': Addresses,
        },
        computed: {
            updateShippingProfileData: function () {
                return {
                    'use_kabooodle_as_shipper': this.usesKabooodleAsShipper ? 1 : 0,
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
        },
        created: function () {
        },
    }
</script>
