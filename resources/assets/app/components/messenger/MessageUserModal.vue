<template>
    <div>
        <modal :modal_id="modal_el_id">
            <template v-if="modal_title" slot="modal_header">{{ modal_title }}</template>
            <template slot="modal_body">
                <div v-if="! display_success">
                    <div class="form-group row">
                        <label class="control-label col-sm-3">Subject</label>
                        <div class="col-sm-9">
                            <input type="text" name="subject" v-model.trim="subject" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row" v-if="! is_direct_to_user">
                        <label class="control-label col-sm-3">Recipient</label>
                        <div class="col-sm-9">
                            <multiselect
                                    v-model="recipient"
                                    id="ajax"
                                    label="full_name"
                                    track-by="id"
                                    placeholder=""
                                    :custom-label="nameWithUsername"
                                    :options="recipients"
                                    :multiple="false"
                                    :searchable="true"
                                    :loading="isLoading"
                                    :internal-search="false"
                                    :clear-on-select="true"
                                    :close-on-select="true"
                                    :options-limit="10"
                                    :limit="10"
                                    @search-change="searchIt">
                                <template slot="noResult" v-show="!isLoading">
                                    <span class="" v-show="!isLoading" >No results found</span>
                                </template>
                                <template slot="option" scope="props">
                                    <div class="option__desc">
                                        <span class="option__title">{{ props.option.full_name }}</span>
                                        <small class="option__small text-muted text-small">({{ props.option.username }})</small>
                                    </div>
                                </template>
                            </multiselect>
                        </div>
                    </div>
                    <div v-else>
                        <input type="hidden" name="recipient" v-model.trim="recipient" :value="recipient_id">
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-3">Message</label>
                        <div class="col-sm-9">
                            <textarea class="form-control " v-model.trim="message" placeholder="Write a message..." name="message" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div v-if="display_success">Your message has been sent.</div>
            </template>
            <template slot="modal_footer">
                <button v-if="! display_success" type="button" class="btn primary btn-sm btn-messenger" @click="storeResponse" :class="sending ? 'disabled' : null" :disabled="sending ? true: false">Send <spinny v-if="sending"></spinny> </button>
                <button type="button" class="btn white btn-sm btn-messenger" @click="closeModal" :class="sending ? 'disabled' : null" :disabled="sending ? true: false">Close</button>
            </template>
        </modal>
    </div>
</template>
<style src="./../multiselect/vue-multiselect.min.css"></style>
<script>
    import currentUser from '../current-user';
    import Multiselect from 'vue-multiselect';
    import Spinny from '../Spinner.vue';
    import Modal from '../Modal.vue';
    export default{
        props : {
            recipient_name : {
                type: String
            },
            recipient_id: {},
            search_endpoint: {
              type: String
            },
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
            title: {
                type: String,
                default: 'Send message to Kabooodle user'
            },
        },
        data(){
            return{
                previousRequest:null,
                isLoading: false,
                recipient: null,
                display_success: false,
                sending: false,
                subject: null,
                message: null,
                recipients:[],
            }
        },
        computed:{
            modal_title(){
                return (this.is_direct_to_user ? 'Send a message to '+this.recipient_name : this.title);
            },
            is_direct_to_user(){
                return (this.direct_to_user === 'true' || this.direct_to_user === true);
            }
        },
        created() {
            if (this.is_direct_to_user) {
                this.recipient = this.recipient_id;
            }
        },
        methods: {
            nameWithUsername ({ full_name, username }) {
                return `${full_name} (${username})`;
            },
            searchIt(query){
                if (query.trim() == '' || ! currentUser()) {
                    return;
                }

                this.isLoading = true;
                this.$http.post(this.search_endpoint, {q: query},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.recipients = response.body.data.data;
                    this.isLoading = false;
                });
            },
            resetState(){
                this.message = null;
                this.subject = null;
                this.sending = false;
            },
            resetModal(){
                this.resetState();
                this.display_success = false;
            },
            closeModal(event){
                $('#'+this.modal_el_id).modal('hide');
                this.resetModal();
            },
            storeResponse(event){
                this.sending = true;

                if (! currentUser()) {
                    notify({
                        type : 'information',
                        text : 'You must be signed in to send a message'
                    });

                    this.sending = false;
                    return false;
                }

                this.$http.post(this.endpoint, {
                    message: this.message,
                    subject: this.subject,
                    recipient: (this.recipient === Object(this.recipient) ? this.recipient.id : this.recipient )
                }).then((response)=>{
                    this.display_success = true;
                }, (response)=>{
                    console.log(response);
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
            'spinny' : Spinny,
            'multiselect' : Multiselect,
            'modal' : Modal
        }
    }
</script>
