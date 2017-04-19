<template>
    <span>
        <a @click="showCancelModal" class="btn btn-xs danger" :class="btnDisabled ? 'disabled' : null">Cancel<spinny v-if="cancelling"></spinny></a>
        <modal :modal_id="modalId" modal_class="cancel-modal" :display_header="false" :display_footer="false">
            <div slot="modal_body">
                <div class="m-a-2 center text-center block-center center-block">
                    <h5 class="m-t-3 m-b-3">Are you sure you want to cancel this claim? This cannot be undone.</h5>
                    <div class="form-group">
                        <textarea id="comment_new_text" v-model="cancelMessage" class="form-control" rows="3" placeholder="I am cancelling this claim because..." style="resize: none;"></textarea>
                    </div>
                    <button type="button" class="m-l-1 btn btn-lg white" @click.prevent="hideCancelModal">Don't cancel claim</button>
                    <button type="button" class="btn btn-lg danger" :class="submittingCancel ? 'disabled' : null" @click.prevent="cancel">Yes, cancel claim<spinny v-if="submittingCancel"></spinny></button>
                </div>
            </div>
        </modal>
    </span>
</template>
<style>
    
</style>
<script>
    import Modal from '../Modal.vue';
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
                cancelling: false,
                submittingCancel: false,
                modalId: 'cancel-modal-'+this.claim.id,
                cancelMessage: null,
            }
        },
        computed: {
            btnDisabled() {
                return !this.claim.cancelable || this.claim.canceled_at || this.cancelling;
            },
            cancelData() {
                return {
                    message: this.cancelMessage
                }
            }
        },
        methods: {
            showCancelModal() {
                this.cancelling = true;
                $('#'+this.modalId).modal('show');
            },
            hideCancelModal() {
                console.log('hiding');
                this.cancelling = false;
                $(document).find('.cancel-modal').modal('hide');
            },
            cancel() {
                this.submittingCancel = true;
                var self = this;
                self.$http.post(self.claimEndpoint + '/cancel', self.cancelData)
                    .then(function (response) {
                        self.hideCancelModal();
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
                        self.submittingCancel = false;
                        self.cancelling = false;
                    });
            }
        },
        components: {
            Modal,
            Spinny
        }
    }
</script>
