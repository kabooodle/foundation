<template>
    <span>
        <button
                :disabled="processing || disable"
                type="button"
                class="btn-message btn white btn-xs "
                :class="processing || disable ? 'disabled' : null"
                @click="openModal"
        >Message
            <spinny v-if="processing"></spinny>
        </button>
        <message-modal
                direct_to_user="true"
                :endpoint="endpoint"
                :modal_el_id="modal_id"
                :recipient_id="recipient_id"
                :recipient_name="recipient_name"
        ></message-modal>
    </span>
</template>
<script>
    import currentUser from '../current-user';
    import MessageModal from './MessageUserModal.vue';
    import Spinny from  '../Spinner.vue';
    export default{
        props : {
            recipient_name : {
                required: true,
                type: String
            },
            recipient_id: {
                required: true,
            },
            endpoint : {
                required: true,
                type: String
            }
        },
        data(){
            return{
                display: true,
                disable: true,
                processing: false,
            }
        },
        created(){
            if (currentUser()) {
                if (this.recipient_id == currentUser().id) {
                    this.disable = true;
                    return;
                }
            }

            this.disable = false;
            return;
        },
        computed: {
          modal_id(){
              return 'kbdl_modal_messenger_'+randomAlphaStr(3);
          }
        },
        methods : {
            openModal(){
                if (currentUser()) {
                    $('#'+this.modal_id).modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                } else {
                    notify({
                        'text': 'You must be signed in in order to message this user.',
                        'type': 'information'
                    });
                }
            }
        },
        components: {
            'spinny' : Spinny,
            'message-modal' : MessageModal
        }
    }
</script>
