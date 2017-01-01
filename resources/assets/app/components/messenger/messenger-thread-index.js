import Messages from './Messages.vue';
import MessageRespond from './MessageRespond.vue';

new Vue({
    el: '#messages_index',
    created(){
        $Bus.$on('messages:fetched', (messages, thread)=>{
            setTimeout(function(){
                $("#messages-body").scrollTop( $( "#messages-body" ).prop( "scrollHeight" ) ).perfectScrollbar('update');
            },0);
        });
    },
    components: {
        'messages' : Messages,
        'message-respond' : MessageRespond
    }
});