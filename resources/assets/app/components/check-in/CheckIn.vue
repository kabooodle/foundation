<template>
    <div>
        <div>
            <p class="text-center">
                <a @click="checkInType = 'user'">Sign In</a>
            </p>
            <div v-show="checkInType === 'user'">
                <sign-in
                        :sign-in-route="signInRoute"
                        :password-reset-route="passwordResetRoute"
                        :csrf="csrf"
                        :redirect="redirect"
                    >
                </sign-in>
                <a @click="checkInType = null">Go Back</a>
            </div>
        </div>
        <div>
            <p class="text-center">
                <a @click="checkInType = 'register'">Register</a>
            </p>
            <div v-show="checkInType === 'register'">
                <register
                        :route="registerRoute"
                        :csrf="csrf"
                        :redirect="redirect"
                    >
                </register>
                <a @click="checkInType = null">Go Back</a>
            </div>
        </div>
        <div>
            <p class="text-center">
                <a @click="checkInType = 'guest'">Continue as a Guest</a>
            </p>
            <div v-show="checkInType === 'guest'">
                <guest-claim
                        :endpoint="guestClaimEndpoint"
                    >
                </guest-claim>
                <a @click="checkInType = null">Go Back</a>
            </div>
        </div>
    </div>
</template>
<script>
    import SignIn from '../sign-in/SignIn.vue';
    import Register from '../register/Register.vue';
    import GuestClaim from '../claims/GuestClaim.vue'
    export default{
        props: {
            signInRoute: {
                type: String,
                required: true
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
            csrf: {
                type: String,
                required: true
            },
            redirect: {
                type: String,
                required: true
            },
        },
        data (){
            return {
                checkInType: null,
            }
        },
        components:{
            'sign-in': SignIn,
            'register': Register,
            'guest-claim': GuestClaim,
        },
    }
</script>
