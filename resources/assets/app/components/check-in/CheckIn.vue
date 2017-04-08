<template>
    <div>
        <div v-show="checkInType === null || checkInType === 'user'">
            <p class="text-center">
                <a @click="checkInType = 'user'" class="text-primary">Sign In</a>
                <span v-show="checkInType === 'user'">
                    or
                    <a @click="checkInType = null" class=text-muted>Go Back</a>
                </span>
            </p>
            <div v-show="checkInType === 'user'">
                <sign-in
                    :request-type="requestType"
                    :sign-in-web-route="signInWebRoute"
                    :sign-in-api-endpoint="signInApiEndpoint"
                    :password-reset-route="passwordResetRoute"
                    :csrf="csrf"
                    :redirect="redirect"
                    v-on:success="performUserSuccess"
                >
                </sign-in>
            </div>
        </div>
        <div v-show="checkInType === null || checkInType === 'register'">
            <p class="text-center">
                <a @click="checkInType = 'register'" class="text-primary">Register</a>
                <span v-show="checkInType === 'register'">
                    or
                    <a @click="checkInType = null" class=text-muted>Go Back</a>
                </span>
            </p>
            <div v-show="checkInType === 'register'">
                <register
                    request-type="web"
                    :route="registerRoute"
                    :csrf="csrf"
                    :redirect="redirect"
                    v-on:success="performUserSuccess"
                >
                </register>
            </div>
        </div>
        <div v-show="checkInType === null || checkInType === 'guest'">
            <p v-if="!guestSuccess" class="text-center">
                <a @click="checkInType = 'guest'" class="text-primary selected">Continue as a Guest</a>
                <span v-show="checkInType === 'guest'">
                    or
                    <a @click="checkInType = null" class=text-muted>Go Back</a>
                </span>
            </p>
            <div v-show="checkInType === 'guest'">
                <guest-claim
                    :claim-endpoint="guestClaimEndpoint"
                    :convert-endpoint="guestConvertEndpoint"
                    :csrf="csrf"
                    v-on:success="performGuestSuccess"
                >
                </guest-claim>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import SignIn from '../sign-in/SignIn.vue';
    import Register from '../register/Register.vue';
    import GuestClaim from '../claims/GuestClaim.vue';
    export default{
        props: {
            requestType: {
                type: String,
                default: 'web'
            },
            signInWebRoute: {
                type: String,
                required: true
            },
            signInApiEndpoint: {
                type: String,
                required: function () {
                    return this.requestType == 'api';
                }
            },
            passwordResetRoute: {
                type: String,
                required: true
            },
            registerRoute: {
                type: String,
                required: true
            },
            guestClaimEndpoint: {
                type: String,
                required: true
            },
            guestConvertEndpoint: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
            redirect: {
                type: String,
                required: true
            },
        },
        data () {
            return {
                checkInType: null,
                userSuccess: false,
                guestSuccess: false,
            }
        },
        components: {
            'sign-in': SignIn,
            'register': Register,
            'guest-claim': GuestClaim,
        },
        methods: {
            performUserSuccess: function () {

            },
            performGuestSuccess: function () {
                this.guestSuccess = true;
            },
        },
    }
</script>
