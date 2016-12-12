<template>
    <form :action="signInRoute" method="POST">
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="_redirect" :value="redirect">
        <div class="md-form-group">
            <input v-model="username" type="text" name="username" class="md-input">
            <label>Username</label>
        </div>

        <div class="md-form-group">
            <input v-model="password" type="password" name="password" class="md-input">
            <label>Password <a :href="passwordResetRoute" class="text-accent text-primary _500 m-l-lg font-italic">Forgot password?</a></label>
        </div>

        <button type="submit" class="btn primary btn-block p-x-md">Login</button>
    </form>
</template>
<style>

</style>
<script>
    export default {
        props: {
            signInRoute: {
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
            }
        },
        computed: {
            signInData: function () {
                return {
                    'username': this.username,
                    'password': this.password
                }
            },
        }
    }
</script>
