<template>
    <div>
        <div v-if="claimSuccess && !ownerClaim">
            <div>
                <p>Fantastic! The item is on hold for you for 5 minutes in order to give you time to verify the claim through the email we just sent you.</p>
                <p>Want to track the progress of your claim? No problem. Become a Kabooodle user now in order to take advantage of that and other benefits.</p>
            </div>
            <form id="guest-convert-form" :action="convertEndpoint" method="POST">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="email" :value="claimEmail">
                <div class="md-form-group">
                    <input v-model="username" type="text" name="username" class="md-input">
                    <label>Username</label>
                </div>

                <div class="md-form-group">
                    <input v-model="password" type="password" name="password" class="md-input">
                    <label>Password</label>
                </div>

                <div class="md-form-group">
                    <input v-model="referrer" type="text" name="referred_by" class="md-input">
                    <label>Referred By User <small class="">(username)</small></label>
                </div>

                <button type="submit" @click.prevent="convertGuestToUser" :disabled="converting" class="btn primary btn-block p-x-md">Register<spinny v-if="converting"></spinny></button>
            </form>
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
                        <label>Phone</label>
                    </div>
                </div>
            </div>

            <p class="">By clicking on "Claim" below, you are agreeing to the <a href="" class="text-info">Terms of Service</a> and the <a href="" class="text-info">Privacy Policy</a>.</p>

            <a @click="claim" :disabled="claiming" class="btn primary btn-block p-x-md">Claim<spinny v-if="claiming"></spinny></a>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import StateInput from '../inputs/StateInput.vue';
    import Spinny from '../Spinner.vue';
    export default {
        props: {
            claimEndpoint: {
                type: String,
                required: true
            },
            convertEndpoint: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
            ownerClaim: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                claiming: false,
                converting: false,
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
                claimEmail: null,
                username: null,
                password: null,
                referrer: null,
                convertSuccess: false,
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
            },
            guestConvertData: function () {
                return {
                    'email': this.claimEmail,
                    'username': this.username,
                    'password': this.password,
                }
            }
        },
        methods: {
            claim: function () {
                var self = this;
                self.claiming = true;
                self.$http.post(
                    self.claimEndpoint,
                    self.guestClaimData
                ).then(function (response) {
                    self.claimSuccess = true;
                    self.claimEmail = self.email;
                    self.$emit('success');
                    notify({
                        'text': !self.ownerClaim ? 'Verify this claim from the email we just sent you!' : 'Item claimed successfully!',
                        'type': 'success'
                    });
                }, function (response) {
                    notify({
                        'text': 'There was a problem with the information you provided. Please try again!',
                        'type': 'error'
                    });
                }).finally(()=>{
                    self.claiming = false;
                });
            },
            convertGuestToUser: function () {
                var self = this;
                self.converting = true;
                $('#guest-convert-form').submit();
                //self.$http.post(
                //    self.convertEndpoint,
                //    self.guestConvertData
                //).then(function (response) {
                //    self.convertSuccess = true;
                //    notify({
                //        'text': 'You are now a Kabooodle user!',
                //        'type': 'success'
                //    });
                //}, function (response) {
                //    notify({
                //        'text': 'There was a problem with the information you provided. Please try again!',
                //        'type': 'error'
                //    });
                //}).finally(()=>{
                //    self.converting = false;
                //});
            },
        }
    }
</script>
