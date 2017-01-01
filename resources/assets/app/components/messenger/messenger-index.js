import Threads from './Threads.vue';
import MessageModal from './MessageUserModal.vue';

new Vue({
    el: '#messages_index',
    components: {
        'threads' : Threads,
        'message-modal' : MessageModal
    }
});