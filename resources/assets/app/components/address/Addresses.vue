<template>
    <div>
        <user-address v-for="(address, index) in addresses" :key="address.id"
              :address=address
              :primary-id=primaryId
              :update-primary-endpoint=updatePrimaryEndpoint
              :addresses-endpoint=addressesEndpoint
              v-on:new-primary=setNewPrimary
              v-on:remove-address="addresses.splice(index, 1)"
        ></user-address>
        <div class="row">
            <div class="col-sm-12">
                <div v-show="addingAddress">
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Full Name</label>
                        <div class="col-sm-6">
                            <input type="text" v-model="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Company Name<small class="block text-muted">(Optional)</small></label>
                        <div class="col-sm-6">
                            <input type="text" v-model="company" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Street 1</label>
                        <div class="col-sm-6">
                            <input type="text" v-model="street1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Street 2<small class="block text-muted">(Optional)</small></label>
                        <div class="col-sm-6">
                            <input type="text" v-model="street2" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">City</label>
                        <div class="col-sm-6">
                            <input type="text" v-model="city" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">State</label>
                        <div class="col-sm-3">
                            <select v-model="state" type="text" name="state" class="form-control">
                                <option v-for="option in stateOptions" :value="option.abbreviation">
                                    {{ option.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Zip</label>
                        <div class="col-sm-3">
                            <input type="text" v-model="zip" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Phone</label>
                        <div class="col-md-4">
                            <input type="text" v-model="phone" class="form-control">
                            <span class="block text-xs text-muted">(USPS requires this for certain shipment types)</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3"></label>
                        <div class="col-sm-3">
                            <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="saveAddress" class="btn primary btn-sm ">
                                Save <spinny v-if="saving"></spinny>
                            </button>
                            <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="addingAddress = !addingAddress" class="btn btn-sm white  ">Cancel</button>
                        </div>
                    </div>
                </div>
                <div v-show="!addingAddress">
                    <div class="pull-left">
                        <button @click.prevent="addingAddress = !addingAddress" class="btn btn-sm white ">Add Address</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Address from '../address/Address.vue'
    import Spinny from '../Spinner.vue';
    export default {
        props: {
            type: {
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
            initialAddresses: Array,
            initialPrimaryId: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                saving: false,
                addresses: this.initialAddresses,
                primaryId: this.initialPrimaryId,
                addingAddress: false,
                name: null,
                company: null,
                street1: null,
                street2: null,
                city: null,
                state: null,
                zip: null,
                phone: null,
                stateOptions: [
                    {
                        "name": "Alabama",
                        "abbreviation": "AL"
                    },
                    {
                        "name": "Alaska",
                        "abbreviation": "AK"
                    },
                    {
                        "name": "American Samoa",
                        "abbreviation": "AS"
                    },
                    {
                        "name": "Arizona",
                        "abbreviation": "AZ"
                    },
                    {
                        "name": "Arkansas",
                        "abbreviation": "AR"
                    },
                    {
                        "name": "California",
                        "abbreviation": "CA"
                    },
                    {
                        "name": "Colorado",
                        "abbreviation": "CO"
                    },
                    {
                        "name": "Connecticut",
                        "abbreviation": "CT"
                    },
                    {
                        "name": "Delaware",
                        "abbreviation": "DE"
                    },
                    {
                        "name": "District Of Columbia",
                        "abbreviation": "DC"
                    },
                    {
                        "name": "Federated States Of Micronesia",
                        "abbreviation": "FM"
                    },
                    {
                        "name": "Florida",
                        "abbreviation": "FL"
                    },
                    {
                        "name": "Georgia",
                        "abbreviation": "GA"
                    },
                    {
                        "name": "Guam",
                        "abbreviation": "GU"
                    },
                    {
                        "name": "Hawaii",
                        "abbreviation": "HI"
                    },
                    {
                        "name": "Idaho",
                        "abbreviation": "ID"
                    },
                    {
                        "name": "Illinois",
                        "abbreviation": "IL"
                    },
                    {
                        "name": "Indiana",
                        "abbreviation": "IN"
                    },
                    {
                        "name": "Iowa",
                        "abbreviation": "IA"
                    },
                    {
                        "name": "Kansas",
                        "abbreviation": "KS"
                    },
                    {
                        "name": "Kentucky",
                        "abbreviation": "KY"
                    },
                    {
                        "name": "Louisiana",
                        "abbreviation": "LA"
                    },
                    {
                        "name": "Maine",
                        "abbreviation": "ME"
                    },
                    {
                        "name": "Marshall Islands",
                        "abbreviation": "MH"
                    },
                    {
                        "name": "Maryland",
                        "abbreviation": "MD"
                    },
                    {
                        "name": "Massachusetts",
                        "abbreviation": "MA"
                    },
                    {
                        "name": "Michigan",
                        "abbreviation": "MI"
                    },
                    {
                        "name": "Minnesota",
                        "abbreviation": "MN"
                    },
                    {
                        "name": "Mississippi",
                        "abbreviation": "MS"
                    },
                    {
                        "name": "Missouri",
                        "abbreviation": "MO"
                    },
                    {
                        "name": "Montana",
                        "abbreviation": "MT"
                    },
                    {
                        "name": "Nebraska",
                        "abbreviation": "NE"
                    },
                    {
                        "name": "Nevada",
                        "abbreviation": "NV"
                    },
                    {
                        "name": "New Hampshire",
                        "abbreviation": "NH"
                    },
                    {
                        "name": "New Jersey",
                        "abbreviation": "NJ"
                    },
                    {
                        "name": "New Mexico",
                        "abbreviation": "NM"
                    },
                    {
                        "name": "New York",
                        "abbreviation": "NY"
                    },
                    {
                        "name": "North Carolina",
                        "abbreviation": "NC"
                    },
                    {
                        "name": "North Dakota",
                        "abbreviation": "ND"
                    },
                    {
                        "name": "Northern Mariana Islands",
                        "abbreviation": "MP"
                    },
                    {
                        "name": "Ohio",
                        "abbreviation": "OH"
                    },
                    {
                        "name": "Oklahoma",
                        "abbreviation": "OK"
                    },
                    {
                        "name": "Oregon",
                        "abbreviation": "OR"
                    },
                    {
                        "name": "Palau",
                        "abbreviation": "PW"
                    },
                    {
                        "name": "Pennsylvania",
                        "abbreviation": "PA"
                    },
                    {
                        "name": "Puerto Rico",
                        "abbreviation": "PR"
                    },
                    {
                        "name": "Rhode Island",
                        "abbreviation": "RI"
                    },
                    {
                        "name": "South Carolina",
                        "abbreviation": "SC"
                    },
                    {
                        "name": "South Dakota",
                        "abbreviation": "SD"
                    },
                    {
                        "name": "Tennessee",
                        "abbreviation": "TN"
                    },
                    {
                        "name": "Texas",
                        "abbreviation": "TX"
                    },
                    {
                        "name": "Utah",
                        "abbreviation": "UT"
                    },
                    {
                        "name": "Vermont",
                        "abbreviation": "VT"
                    },
                    {
                        "name": "Virgin Islands",
                        "abbreviation": "VI"
                    },
                    {
                        "name": "Virginia",
                        "abbreviation": "VA"
                    },
                    {
                        "name": "Washington",
                        "abbreviation": "WA"
                    },
                    {
                        "name": "West Virginia",
                        "abbreviation": "WV"
                    },
                    {
                        "name": "Wisconsin",
                        "abbreviation": "WI"
                    },
                    {
                        "name": "Wyoming",
                        "abbreviation": "WY"
                    }
                ],
            }
        },
        components:{
            'user-address': Address,
            'spinny' : Spinny
        },
        computed: {
            primary: function () {
                return this.primaryId == 0 ? 1 : 0;
            },
            addressData: function () {
                return {
                    'type': this.type,
                    'primary': this.primary,
                    'name': this.name,
                    'company': this.company,
                    'street1': this.street1,
                    'street2': this.street2,
                    'city': this.city,
                    'state': this.state,
                    'zip': this.zip,
                    'phone': this.phone,
                }
            },
        },
        methods: {
            setNewPrimary: function (id) {
                this.primaryId = id;
            },
            saveAddress: function () {
                this.saving = true;
                this.$http.post(this.addressesEndpoint, this.addressData)
                    .then(function (response) {
                        let new_address = response.data.data.address;
                        this.addresses.push(new_address)
                        if (new_address.primary == true) {
                            this.setNewPrimary(new_address.id);
                        }
                        this.addingAddress = false;
                        this.clearAddressData();
                        notify({
                            'text': 'Your new address has been saved!',
                            'type': 'success'
                        });
                    }, function (response) {
                        notify({
                            'text': response.body.data.msg,
                            'type': 'error'
                        });
                    }).finally(()=>{
                        this.saving = false;
                });
            },
            clearAddressData: function () {
                this.name = null;
                this.company = null;
                this.street1 = null;
                this.street2 = null;
                this.city = null;
                this.state = null;
                this.zip = null;
                this.phone = null;
            },
        }
    }
</script>
