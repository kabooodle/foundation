<template>
    <span class="fb-img-wrapper">
        <template v-if="authorizedAndLoggedIn">
                <img src="/assets/images/icons/FB-login-xs.png">
                <img src="/assets/images/icons/FB-logout-xs.png">
        </template>
        <template v-if="!authorizedAndLoggedIn">
                <img src="/assets/images/icons/FB-login-xs.png">
                <img src="/assets/images/icons/FB-logout-xs.png">
        </template>
    </span>
</template>
<script>
    export default{
        data(){
            return {
                authorized: false,
                logged_in: false,
                attempts: 0
            }
        },
        computed : {
            authorizedAndLoggedIn(){
                return this.logged_in && this.authorized;
            },
        },
        methods : {
            checkLoginState(){
                FB.getLoginStatus((response)=>{
                    this.statusChangeCallback(response);
                });
            },
            statusChangeCallback(response){
                if (response.status === 'connected') {
                    // Logged into your app and Facebook.
                    this.storeUserStatus(response);
                } else if (response.status === 'not_authorized') {
                    // logged in but not authorized
                } else {
                    // not logged in
                }
            },
            FBLogin(){
                FB.login((response)=>{
                    if (response.authResponse) {
                        // Logged in
                        this.storeUserStatus(response);
                    } else {
                        // cancelled or error
                        // No we invalidate the user's shtuff?
                    }
                });
                return false;
            },
            storeUserStatus(response){
                this.$http.post().then((response)=>{
                    this.authorized = true;
                    this.logged_in = true;
                }, (response)=>{
                    this.authorized = false;
                    this.logged_in = false;
                });
            }
        }
    }
</script>
