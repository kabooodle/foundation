<template>
    <div class="form-group row">
        <div class="col-sm-8">
            <span>{{ address }}</span>
            <span v-show="isVerified" data-toggle="tooltip" title="Verified">
                <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
            </span>
            <span v-show="isPrimaryEmail" class="label label-primary">Primary</span>
            <span v-show="!isVerified">
                <div class="pull-right">
                    <button @click="resendVerification" class="btn white btn-sm">
                        Resend Verification
                    </button>
                </div>
            </span>
        </div>
        <div class="col-sm-3">
            <div v-show="!isPrimaryEmail">
                <button v-if="isVerified" @click="makePrimary" class="btn white btn-sm">Make Primary</button>
                <button v-else @click="notifyNeedsToVerify" disabled class="btn disabled white btn-sm">Make Primary</button>
            </div>
        </div>
        <div class="col-sm-1">
            <a href="javascript:;" v-show="!isPrimaryEmail" @click="destroy">
                <i class="fa fa-times text-danger" aria-hidden="true"></i>
            </a>
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
            emailsEndpoint: {
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
            }
        },
        data() {
            return {
                isPrimary: this.initialPrimary,
            }
        },
        computed: {
            isPrimaryEmail: function () {
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
            makePrimary: function (event) {
                event.preventDefault();
                let el = event.target;
                let innerHtml = el.innerHTML;
                el.classList.add('disabled');
                el.disabled = true;
                el.innerHTML = innerHtml + (spinny());
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
                    }).finally(()=>{
                    el.disabled = false;
                    el.classList.remove('disabled');
                    el.innerHTML = innerHtml
                });
            },
            resendVerification: function (event) {
                event.preventDefault();
                let el = event.target;
                let innerHtml = el.innerHTML;
                el.classList.add('disabled');
                el.disabled = true;
                el.innerHTML = innerHtml + (spinny());
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
                    }).finally(()=>{
                    el.disabled = false;
                    el.classList.remove('disabled');
                    el.innerHTML = innerHtml
                });
            },
            destroy: function () {
                var self = this;
                confirmModal(function () {
                    self.$http.delete(self.emailsEndpoint+'/'+self.id)
                    .then(function (response) {
                        $.noty.closeAll();
                        self.$emit('remove-email');
                        notify({
                            'text': 'That email address has been deleted.',
                            'type': 'success'
                        });
                    }, function (response) {
                        $.noty.closeAll();
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
                }, function () {
                    $.noty.close();
                }, {text: 'Are you sure you want to delete this email address?'});
            }
        },
    }
</script>
