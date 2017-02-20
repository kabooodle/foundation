<template>
    <span class="fb-img-wrapper">
        <template v-if="authorizedAndLoggedIn">
                <spinny v-if="refreshing"></spinny>
                <a
                        :class="refreshing ? 'disabled' : null"
                        href="javascript:;"
                        @click="FBRefresh"
                        type="button">
                    <img :src="refresh_icon">
                </a>
        </template>
        <template v-if="!authorizedAndLoggedIn">
                <a
                        :class="refreshing ? 'disabled' : null"
                        href="javascript:;"
                        @click="FBLogin"
                        type="button">
                    <img :src="login_icon">
                </a>
        </template>
    </span>
</template>
<script>
    import Spinny from '../Spinner.vue';
    export default{
        props: {
            refresh_endpoint : {
                required: true,
                type: String
            },
            login_icon : {
                type: String,
                default: '/assets/images/icons/FB-login-xs.png'
            },
            refresh_icon_disabled : {
                type: String,
                default: '/assets/images/icons/FB-refresh-disabled-xs.png'
            },
            refresh_icon_original : {
                type: String,
                default: '/assets/images/icons/FB-refresh-xs.png',
            }
        },
        data(){
            return {
                actions: {
                    login_state_checked: false
                },
                refresh_icon: this.refresh_icon_original,
                refreshing: false,
                authorized: false,
                connected: false,
                attempts: 0,
                fbAuth: {
                    token : null,
                    userId: null,
                    expiresIn: null, // days
                    signedRequest: null
                }
            }
        },
        watch : {
            'refreshing' :function(){
                if (this.refreshing == true) {
                    this.refresh_icon = this.refresh_icon_disabled;
                    $Bus.$emit('facebook:refreshing', this.refreshing);
                } else {
                    this.refresh_icon = this.refresh_icon_original;
                }
            },
        },
        mounted(){
            $(document).on('fbload', ()=>{
                this.checkLoginState();
            });
        },
        created(){
            if (! this.actions.login_state_checked) {
                this.checkLoginState();
            }
        },
        computed : {
            authorizedAndLoggedIn(){
                return this.connected && this.authorized;
            },
        },
        methods : {
            checkLoginState(){
                FB.getLoginStatus((response)=>{
                    this.statusChangeCallback(response);
                });
                this.actions.login_state_checked = true;
            },
            statusChangeCallback(response){
                if (response.status === 'connected') {
                    this.handleSuccessfulConnection(response);

                    return response;
                }
                this.handleUnsuccessfulConnection(response);

                return false;
            },
            handleSuccessfulConnection(response){
                this.fbAuth = response.authResponse;
                this.authorized = true;
                this.connected = true;
            },
            handleUnsuccessfulConnection(){
                this.fbAuth = {},
                    this.authorized = false;
                this.connected = false;
            },
            storeUserStatus(callback){
                return this.$http.post(this.refresh_endpoint, this.fbAuth).then((response)=>{
                    this.authorized = true;
                    this.connected = true;

                    $Bus.$emit('facebook:refreshed', this.fbAuth, response.body.data);

                    return callback();
                }, (response)=>{
                    this.authorized = false;
                    this.connected = false;
                    return false;
                });
            },
            FBRefresh(){
                this.attempts++;
                this.refreshing = true;
                this.checkLoginState();
                if (this.authorizedAndLoggedIn) {
                    return this.storeUserStatus(()=>{
                        this.refreshing = false;
                        return true;
                    });
                }

                if (this.attempts <= 4) {
                    return this.FBLogin();
                } else {
                    notify({
                        text: 'An error occurred. Please refresh the page and try again.'
                    });
                    this.refreshing = false;
                    this.attempts = 0;
                    this.handleUnsuccessfulConnection();

                    return false;
                }
            },
            FBLogout(){
                FB.logout((response)=>{
                    this.handleUnsuccessfulConnection();
                });
            },
            FBLogin(){
                return FB.login((response)=>{
                    this.statusChangeCallback(response);
                    this.FBRefresh();
                }, {scope: 'email,user_managed_groups,publish_actions'});
            },
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
