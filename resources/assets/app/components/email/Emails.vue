<template>
    <div>
        <email v-for="(email, index) in emails" :key="email.id"
            :address=email.address
            :id=email.id
            :primary-id=primaryId
            :is-verified=email.verified
            :update-primary-endpoint=updatePrimaryEndpoint
            :resend-verification-endpoint=resendVerificationEndpoint
            :emails-endpoint=emailsEndpoint
            v-on:new-primary=setNewPrimary
            v-on:resend-verification=resendVerification
            v-on:remove-email="emails.splice(index, 1)"
        ></email>
        <div class="row">
            <div class="col-sm-12">
                <div v-show="addingEmail">
                    <div class="form-group">
                        <input type="text" v-model="newAddress" class="form-control" placeholder="Enter a valid email address">
                    </div>
                    <div class="pull-left">
                        <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="saveEmail" class="btn primary  btn-sm ">
                            Save
                            <spinny v-if="saving"></spinny>
                        </button>
                        <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="addingEmail = !addingEmail" class="btn btn-sm white">Cancel</button>
                    </div>
                </div>
                <div v-show="!addingEmail">
                    <div class="pull-left">
                        <button @click="addingEmail = !addingEmail" class="btn btn-sm white ">Add Email</button>
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
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            initialEmails: Array,
            initialPrimaryId: {
                type: Number,
                required: true,
            },
            emailsEndpoint: {
                type: String,
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
                saving : false,
                emails: this.initialEmails,
                primaryId: this.initialPrimaryId,
                addingEmail: false,
                newAddress: null,
            }
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
                this.saving = true;
                this.$http.post(this.newEmailEndpoint, this.newEmailData)
                    .then((response)=>{
                        this.emails.push(response.data.data.email);
                        this.addingEmail = false;
                        notify({
                            'text': 'Your new address has been saved. Please verify the address in the email we just sent you!',
                            'type': 'success'
                        });
                    }, (response)=>{
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    }).finally(()=>{
                        this.saving = false;
                        this.newAddress = null;
                    });
            },
        },
        components:{
            'email': Email,
            'spinny' : Spinny
        }
    }
</script>
