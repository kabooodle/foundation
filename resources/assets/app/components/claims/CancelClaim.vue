<template>
    <a @click="cancel" class="btn btn-xs danger" :class="btnDisabled ? 'disabled' : null">Cancel<spinny v-if="cancelling"></spinny></a>
</template>
<style>
    
</style>
<script>
    import Spinny from '../Spinner.vue';
    export default {
        props: {
            claim: {
                type: Object,
                required: true
            },
            claimEndpoint: {
                type: String,
                required: true
            }
        },
        data () {
            return {
                cancelling: false
            }
        },
        computed: {
            btnDisabled() {
                return !this.claim.cancelable || this.claim.canceled_at || this.cancelling;
            }
        },
        methods: {
            cancel() {
                this.cancelling = true;
                var self = this;
                confirmModal(function () {
                    self.$http.post(self.claimEndpoint + '/cancel')
                    .then(function (response) {
                        $.noty.closeAll();
                        self.claim.canceled_at = response.body.data.claim.canceled_at;
                        self.$emit('canceled');
                        notify({
                            'text': 'Your claim has been canceled.',
                            'type': 'success'
                        });
                    }, function (response) {
                        $.noty.closeAll();
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    }).finally(function () {
                        self.cancelling = false;
                    });
                }, function () {
                    $.noty.close();
                    self.cancelling = false;
                }, {text: 'Are you sure you want to cancel this claim? This can not be undone.'});
            }
        },
        components: {
            Spinny
        }
    }
</script>
