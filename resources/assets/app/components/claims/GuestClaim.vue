<template>
    <div>
        <div v-if="claimSuccess">
            <span class="text-center text-muted">
                Boo Yah!!
            </span>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-xs-6">
                    <div class="md-form-group">
                        <input v-model="firstName" type="text" name="first_name" class="md-input">
                        <label>First Name</label>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="md-form-group">
                        <input v-model="lastName" type="text" name="last_name" class="md-input">
                        <label>Last Name</label>
                    </div>
                </div>
            </div>

            <div class="md-form-group">
                <input v-model="email" type="text" name="email" class="md-input">
                <label>Email Address</label>
            </div>

            <div class="md-form-group">
                <input v-model="company" type="text" name="company" class="md-input">
                <label>Company Name <small>(Optional)</small></label>
            </div>

            <div class="md-form-group">
                <input v-model="street1" type="text" name="street1" class="md-input">
                <label>Street 1</label>
            </div>

            <div class="md-form-group">
                <input v-model="street2" type="text" name="street2" class="md-input">
                <label>Street 2 <small>(Optional)</small></label>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="md-form-group">
                        <input v-model="city" type="text" name="city" class="md-input">
                        <label>City</label>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="md-form-group">
                        <select v-model="state" type="text" name="state" class="md-input">
                            <option v-for="option in stateOptions" :value="option.abbreviation">
                                {{ option.name }}
                            </option>
                        </select>
                        <label>State</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="md-form-group">
                        <input v-model="zip" type="text" name="zip" class="md-input">
                        <label>Zip</label>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="md-form-group">
                        <input v-model="phone" type="text" name="phone" class="md-input">
                        <label>Phone <small>(Optional)</small></label>
                    </div>
                </div>
            </div>

            <p class="">By clicking on "Claim" below, you are agreeing to the <a href="" class="text-info">Terms of Service</a> and the <a href="" class="text-info">Privacy Policy</a>.</p>

            <button @click="claim" type="submit" class="btn primary btn-block p-x-md">Claim</button>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import StateInput from '../inputs/StateInput.vue';
    export default {
        props: {
            endpoint: {
                type: String,
                required: true
            },
        },
        data(){
            return {
                firstName: null,
                lastName: null,
                email: null,
                company: null,
                street1: null,
                street2: null,
                city: null,
                state: null,
                zip: null,
                phone: null,
                claimSuccess: false,
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
        components: {
            'state-input': StateInput,
        },
        computed: {
            guestClaimData: function () {
                return {
                    'first_name': this.firstName,
                    'last_name': this.lastName,
                    'email': this.email,
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
            claim: function () {
                this.$http.post(
                    this.endpoint,
                    this.guestClaimData
                ).then(function (response) {
                    this.claimSuccess = true;
                    this.$emit('success');
                    notify({
                        'text': 'Verify this claim from the email we just sent you!',
                        'type': 'success'
                    });
                }, function (response) {
                    notify({
                        'text': 'There was a problem with the information you provided. Please try again!',
                        'type': 'error'
                    });
                });
            }
        }
    }
</script>
