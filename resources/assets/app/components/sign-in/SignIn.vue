<template>
    <div>
        <form id="sign-in-form" :action="signInWebRoute" method="POST">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="_redirect" :value="redirect">
            <div class="md-form-group">
                <input v-model="username" type="text" name="username" class="md-input">
                <label>Username</label>
            </div>

            <div class="md-form-group">
                <input v-model="password" type="password" name="password" class="md-input">
                <label class="clearfix" style="width: 100%;">
                    <span class="pull-left">Password</span>
                    <a :href="passwordResetRoute" class="text-primary _500 pull-right font-italic">Forgot password?</a></label>
            </div>

            <button v-if="webRequest" @click.prevent="attemptAuth" :disabled="attemptingAuth" class="btn primary btn-block p-x-md">Sign In<spinny v-if="attemptingAuth"></spinny></button>
            <a v-else @click.prevent="attemptAuth" :disabled="attemptingAuth" class="btn primary btn-block p-x-md" :class="attemptingAuth ? 'disabled': null">Sign In<spinny v-if="attemptingAuth"></spinny></a>
        </form>
    </div>
</template>
<style>

</style>
<script>
    import Spinny from '../Spinner.vue';
    export default {
        props: {
            requestType: {
                type: String,
                default: 'web'
            },
            signInApiEndpoint: {
                type: String,
                required: function () {
                    return this.requestType == 'api';
                }
            },
            signInWebRoute: {
                type: String,
                required: true
            },
            passwordResetRoute: {
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
                username: null,
                password: null,
                attemptingAuth: false,
                webRequest: this.requestType == 'web',
            }
        },
        computed: {
            signInData: function () {
                return {
                    'username': this.username,
                    'password': this.password
                }
            },
        },
        methods: {
            attemptAuth: function () {
                var self = this;
                self.attemptingAuth = true;
                if (self.webRequest) {
                    $('#sign-in-form').submit()
                } else {
                    self.$http.post(self.signInApiEndpoint, self.signInData)
                        .then(function (response) {
                            $('#sign-in-form').submit()
                            //notify({
                                //'text': 'Your login attempt was successful! Hold on while we refresh the page.',
                                //'type': 'success'
                            //});
                            //window.location.href = self.redirect;
                        }, function (response) {
                            self.attemptingAuth = false;
                            notify({
                                'text': response.body.data.msg,
                                'type': 'error'
                            });
                        }).finally(()=>{

                    });
                }
            },
        },
        components: {
            'spinny': Spinny,
        }
    }
</script>
