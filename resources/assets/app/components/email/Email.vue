<template>
    <div class="form-group row">
        <div class="col-sm-9">
            <span>{{ address }}</span>
            <span v-show="isVerified">
                <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
            </span>
            <span v-show="!isVerified">
                <div class="pull-right">
                    <button @click="resendVerification" class="btn primary btn-block p-x-md">Resend Verification</button>
                </div>
            </span>
        </div>
        <div class="col-sm-3">
            <div v-show="isPrimary">
                <div class="text-primary text-center">Primary</div>
            </div>
            <div v-show="!isPrimary">
                <button v-if="isVerified" @click="makePrimary" class="btn white btn-block p-x-md">Make Primary</button>
                <button v-else @click="notifyNeedsToVerify" class="btn disabled white btn-block p-x-md">Make Primary</button>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default {
        props: {
            isInput: {
                type: Boolean,
                default: false,
            },
            name: {
                type: String,
                required: false,
            },
            id: {
                type: Number,
                required: true,
            },
            address: {
                type: String,
                required: false,
            },
            primaryId: {
                type: Number,
                required: true,
            },
            isVerified: {
                type: Boolean,
                default: false,
            },
            updatePrimaryEndpoint: {
                type: String,
                required: true,
            },
            resendVerificationEndpoint: {
                type: String,
                required: true,
            }
        },
        data() {
            return {
                isPrimary: this.initialPrimary,
            }
        },
        computed: {
            isPrimary: function () {
                return this.primaryId == this.id
            },
            updatePrimaryData: function () {
                return {
                    'email_id': this.id,
                }
            },
            resendVerificationData: function () {
                return {
                    'email_id': this.id,
                }
            },
        },
        methods: {
            notifyNeedsToVerify: function () {
                notify({
                    'text': 'Your must verify your email address before you can make it your primary email!',
                    'type': 'information'
                });
            },
            makePrimary: function () {
                this.$http.put(this.updatePrimaryEndpoint, this.updatePrimaryData)
                    .then(function (response) {
                        this.$emit('new-primary', this.id);
                        notify({
                            'text': 'Your primary email address has been updated!',
                            'type': 'success'
                        });
                    }, function (response) {
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
            },
            resendVerification: function () {
                this.$http.put(this.resendVerificationEndpoint, this.resendVerificationData)
                    .then(function (response) {
                        notify({
                            'text': 'A new email verification has been sent to this address!',
                            'type': 'success'
                        });
                    }, function (response) {
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
            }
        },
    }
</script>
