<template>
    <form :action="route" method="POST">
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="_redirect" :value="redirect">
        <div class="md-form-group">
            <input v-model="firstName" type="text" name="first_name" class="md-input">
            <label>First Name</label>
        </div>

        <div class="md-form-group">
            <input v-model="lastName" type="text" name="last_name" class="md-input">
            <label>Last Name</label>
        </div>

        <div class="md-form-group">
            <input v-model="username" type="text" name="username" class="md-input">
            <label>Username</label>
        </div>

        <div class="md-form-group">
            <input v-model="email" type="text" name="email" class="md-input">
            <label>Email Address</label>
        </div>

        <div class="md-form-group">
            <input v-model="password" type="password" name="password" class="md-input">
            <label>Password</label>
        </div>

        <div class="md-form-group">
            <input v-model="referredBy" type="text" name="referred_by" class="md-input">
            <label>Referred By User <small class="">(username or email)</small></label>
        </div>

        <p class="">By clicking on "Create Account" below, you are agreeing to the <a href="" class="text-info">Terms of Service</a> and the <a href="" class="text-info">Privacy Policy</a>.</p>

        <button type="submit" class="btn primary btn-block p-x-md">Create Account</button>
    </form>
</template>
<style>

</style>
<script>
    export default{
        props: {
            route: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
            redirect: String,
        },
        data() {
            return {
                firstName: null,
                lastName: null,
                username: null,
                email: null,
                password: null,
                referredBy: null,
            }
        },
        computed: {
            registerData: function () {
                return {
                    'first_name': this.firstName,
                    'last_name': this.lastName,
                    'username': this.username,
                    'email': this.email,
                    'password': this.password,
                    'referred_by': this.referredBy,
                }
            }
        },
    }
</script>
