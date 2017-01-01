<template>
    <div>
        <div class="modal" :id="modal_el_id">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ title ? : 'Send message' }}</h6>
                    </div>
                    <div class="modal-body">
                        <div v-if="! display_success">
                            <div class="form-group row">
                                <label class="control-label col-sm-3">Subject</label>
                                <div class="col-sm-9">
                                    <input type="text" name="subject" v-model.trim="subject" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row" v-if="! is_direct_to_user">
                                <label class="control-label col-sm-3">Recipient(s)</label>
                                <div class="col-sm-9">
                                    <input type="text" name="recipients[]"  v-model.trim="recipients" class="form-control">
                                </div>
                            </div>
                            <div v-else>
                                <input type="hidden" name="recipients[]" v-model.trim="recipients" :value="recipient_id">
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3">Message</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control " v-model.trim="message" placeholder="Write a message..." name="message" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div v-if="display_success">Your message has been sent.</div>
                    </div>
                    <div class="modal-footer" >
                        <button v-if="! display_success" type="button" class="btn primary btn-sm btn-messenger" @click="storeResponse" :class="sending ? 'disabled' : null" :disabled="sending ? true: false">Send <spinny v-if="sending"></spinny> </button>
                        <button type="button" class="btn white btn-sm btn-messenger" @click="closeModal" :class="sending ? 'disabled' : null" :disabled="sending ? true: false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    import Spinny from '../Spinner.vue';
    export default{
        props : {
            recipient_name : {
                type: String
            },
            recipient_id: {},
            endpoint : {
                required: true,
                type: String
            },
            modal_el_id: {
                required: true,
                type: String
            },
            direct_to_user : {
                default: true
            },
        },
        data(){
            return{
                recipients: null,
                display_success: false,
                sending: false,
                subject: null,
                message: null,
                _title: 'Send message'
            }
        },
        computed:{
            title(){
                return this.is_direct_to_user ? 'Send a message to '+this.recipient_name : this._title;
            },
            is_direct_to_user(){
                return (this.direct_to_user === 'true' || this.direct_to_user === true);
            }
        },
        created() {
            console.log(this._title);
            if (this.is_direct_to_user) {
                this.recipients = this.recipient_id;
            }
        },
        methods: {
            resetState(){
                this.message = null;
                this.subject = null;
                this.sending = false;
                this.display_success = false;
            },
            resetModal(){
                this.resetState();
            },
            closeModal(event){
                $('#'+this.modal_el_id).modal('hide');
                this.resetModal();
            },
            storeResponse(event){
                this.sending = true;
                this.$http.post(this.endpoint, {
                    message: this.message,
                    subject: this.subject,
                    recipients: this.recipients
                }).then((response)=>{
                    this.display_success = true;
                }, (response)=>{
                    if (response.body.data.msg) {
                        notify({
                            text: response.body.data.msg
                        });
                    }
                }).finally(()=>{
                    this.resetState();
                });
            }
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
