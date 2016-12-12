<template>
    <div>
        <div class="md-form-group">
            <input v-model="firstName" type="text" name="first_name" class="md-input">
            <label>First Name</label>
        </div>

        <div class="md-form-group">
            <input v-model="lastName" type="text" name="last_name" class="md-input">
            <label>Last Name</label>
        </div>

        <div class="md-form-group">
            <input v-model="email" type="text" name="email" class="md-input">
            <label>Email Address</label>
        </div>

        <p class="">By clicking on "Claim" below, you are agreeing to the <a href="" class="text-info">Terms of Service</a> and the <a href="" class="text-info">Privacy Policy</a>.</p>

        <button @click="claim" type="submit" class="btn primary btn-block p-x-md">Claim</button>
    </div>
</template>
<style>

</style>
<script>
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
            }
        },
        computed: {
            guestClaimData: function () {
                return {
                    'first_name': this.firstName,
                    'last_name': this.lastName,
                    'email': this.email,
                }
            }
        },
        methods: {
            claim: function () {
                this.$http.post(
                    this.endpoint,
                    this.guestClaimData
                ).then(function (response) {
                    console.log(response);
                }, function (response) {
                    console.log(response);
                });
            }
        }
    }
</script>
