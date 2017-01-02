<template>
    <div>
        <div class="modal" :id="modal_el_id">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ title }}</h6>
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
                                <label class="control-label col-sm-3">Recipient</label>
                                <div class="col-sm-9">
                                    <multiselect
                                            v-model="recipient"
                                            id="ajax"
                                            label="full_name"
                                            track-by="id"
                                            placeholder="Type to search for a recipient"
                                            :custom-label="nameWithUsername"
                                            :options="recipients"
                                            :multiple="false"
                                            :searchable="true"
                                            :loading="isLoading"
                                            :internal-search="false"
                                            :clear-on-select="false"
                                            :close-on-select="false"
                                            :options-limit="10"
                                            :limit="3"
                                            @search-change="searchIt">
                                        <template slot="option" scope="props">
                                            <div class="option__desc">
                                                <span class="option__title">{{ props.option.full_name }}</span>
                                                <span class="option__small">({{ props.option.username }})</span>
                                            </div>
                                        </template>
                                        <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
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
    import Multiselect from 'vue-multiselect';
    import Spinny from '../Spinner.vue';
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
        },
        data(){
            return{
                isLoading: false,
                recipient: null,
                display_success: false,
                sending: false,
                subject: null,
                message: null,
                recipients:[],
                _defaultTitle: 'Send message'
            }
        },
        computed:{
            title(){
                return this.is_direct_to_user ? 'Send a message to '+this.recipient_name : this._defaultTitle;
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
                return `${full_name} — [${username}]`
            },
            searchIt(query){
                this.isLoading = true
                this.$http.post(this.search_endpoint, query).then((response) => {
                    this.recipients = response.body.data.data;
                    this.isLoading = false;
                });
            },
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
                    recipient: this.recipient
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
            'spinny' : Spinny,
            'multiselect' : Multiselect
        }
    }
</script>
