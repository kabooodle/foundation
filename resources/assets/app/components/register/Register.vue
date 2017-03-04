<template>
    <form :action="route" method="POST">
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="_redirect" :value="redirect">

        <div class="row">
            <div class="col-xs-12">
                <div class="md-form-group">
                    <select class="md-input" name="account_type" data-toggle="selectpicker" data-style="btn white" data-width="100%">
                        <option data-subtext="Always free" value="basic" :selected="selected_account == 'basic'">Basic</option>
                        <option data-subtext="30 day free trial" value="merchant" :selected="selected_account == 'merchant'">Merchant</option>
                        <option data-subtext="30 day free trial" value="merchant" :selected="selected_account == 'merchant_plus'">Merchant Plus</option>
                    </select>
                    <label>Account Type:</label>
                </div>
            </div>
        </div>

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
            <input v-model="referrer" type="text" name="referred_by" class="md-input">
            <label>Referred By User <small class="">(username)</small></label>
        </div>

        <p class="text-sm">By clicking on "Create Account" below, you are agreeing to the <a href="/legal/terms-service" class="text-primary">Terms of Service</a> and the <a href="/legal/privacy" class="text-primary">Privacy Policy</a>.</p>

        <button type="submit" class="btn primary btn-block p-x-md">Create Account</button>
    </form>
</template>
<style>

</style>
<script>
    export default{
        props: {
            referrer: {
                type: String,
                default : null,
            },
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
                query_params: {},
                selected_account: 'basic',
                accountType: 'basic',
                firstName: null,
                lastName: null,
                username: null,
                email: null,
                password: null,
            }
        },
        created(){
            this.checkQueryString(location.search);
        },
        methods: {
            checkQueryString(query){
                    if (!query) {
                        return { };
                    }

                    if(/[?&]a=/.test(location.search)) {
                        let params = (/^[?#]/.test(query) ? query.slice(1) : query)
                            .split('&')
                            .reduce((params, param) => {
                                let [ key, value ] = param.split('=');
                                params[key] = value ? decodeURIComponent(value.replace(/\+/g, ' ')) : '';
                                return params;
                            }, { });

                        this.query_params = params;
                        this.selected_account = params.a;
                    }
            },
        },
        computed: {
            registerData: function () {
                return {
                    'account_type' : this.accountType,
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
