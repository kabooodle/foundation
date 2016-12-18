<template>

    <div>
        <email v-for="email in emails"
            :address=email.address
            :id=email.id
            :primary-id=primaryId
            :is-verified=email.verified
            :update-primary-endpoint=updatePrimaryEndpoint
            :resend-verification-endpoint=resendVerificationEndpoint
            v-on:new-primary=setNewPrimary
            v-on:resend-verification=resendVerification
        ></email>
        <div class="row">
            <div class="col-sm-12">
                <div v-show="addingEmail">
                    <div class="form-group">
                        <input type="text" v-model="newAddress" class="form-control">
                    </div>
                    <div class="pull-left">
                        <button @click="addingEmail = !addingEmail" class="btn white btn-block p-x-md">Cancel</button>
                    </div>
                    <div class="pull-right">
                        <button @click="saveEmail" class="btn primary btn-block p-x-md">Save</button>
                    </div>
                </div>
                <div v-show="!addingEmail">
                    <div class="pull-left">
                        <button @click="addingEmail = !addingEmail" class="btn white btn-block p-x-md">Add Email</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    import Email from './Email.vue'
    export default {
        props: {
            initialEmails: Array,
            initialPrimaryId: {
                type: Number,
                required: true,
            },
            newEmailEndpoint: {
                type: String,
                required: true,
            },
            updatePrimaryEndpoint: {
                type: String,
                required: true,
            },
            resendVerificationEndpoint: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                emails: this.initialEmails,
                primaryId: this.initialPrimaryId,
                addingEmail: false,
                newAddress: null,
            }
        },
        components:{
            'email': Email,
        },
        computed: {
            newEmailData: function () {
                return {
                    'address': this.newAddress
                }
            }
        },
        methods: {
            setNewPrimary: function (id) {
                this.primaryId = id;
            },
            resendVerification: function (id) {
                console.log(id);
            },
            saveEmail: function () {
                this.$http.post(this.newEmailEndpoint, this.newEmailData)
                    .then(function (response) {
                        this.emails.push(response.data.data.email);
                        this.addingEmail = false;
                        notify({
                            'text': 'Your new address has been saved. Please verify the address in the email we just sent you!',
                            'type': 'success'
                        });
                    }, function (response) {
                        console.log(response);
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
            },
        },
    }
</script>
