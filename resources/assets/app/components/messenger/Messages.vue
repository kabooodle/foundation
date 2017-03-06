<template>
    <div>
        <spinny v-if="fetching" :size="'' + 28" class="text-center center-block" ></spinny>
        <message
                v-if="messages"
                v-for="message in messages"
                :key="message.id"
                :message="message"
        ></message>
    </div>
</template>
<script>
    import currentUser from '../current-user';
    import Spinny from  '../Spinner.vue';
    import Message from './Message.vue';
    export default{
        props: {
            read_endpoint: {
                required: true,
                type: String
            },
            endpoint: {
                required: true,
                type: String
            },
            thread: {
                required: true,
                type: Object
            }
        },
        data(){
            return {
                messages: [],
                fetching: false,
            }
        },
        mounted(){
            this.fetchThread();
        },
        created(){
            let messageChannel = KABOOODLE_APP.pusher.subscribe('presence.'+KABOOODLE_APP.env+'.threads.'+this.thread.id);
            messageChannel.bind('response:added', (data)=>{
                this.messages.push(data.message);
                $Bus.$emit('messages:fetched', this.thread, this.messages);
            });

            $Bus.$on('messages:fetched', (thread, messages)=>{
                this.flagAsRead(thread,messages);
            });
        },
        methods: {
            fetchThread(){
                this.fetching = true;
                this.$http.get(this.endpoint).then((response)=>{
                    this.messages = response.body.data;
                    this.fetching = false;
                    $Bus.$emit('messages:fetched', this.thread, this.messages);
                }, (response)=>{});
            },
            flagAsRead(thread, messages){
                this.$http.post(this.read_endpoint, {participant_id: currentUser().id});
            },
        },
        components: {
            'message' : Message,
            'spinny' : Spinny
        }
    }
</script>
