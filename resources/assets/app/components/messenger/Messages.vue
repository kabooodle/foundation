<template>
    <div>
        <spinny v-if="fetching" :size="'' + 28" class="text-center center-block" ></spinny>
        <message
                v-if="messages"
                v-for="message in messages"
                :message="message"
        ></message>
    </div>
</template>
<script>
    import Spinny from  '../Spinner.vue';
    import Message from './Message.vue';
    export default{
        props: {
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
        },
        methods: {
            fetchThread(){
                this.fetching = true;
                this.$http.get(this.endpoint).then((response)=>{
                    this.messages = response.body.data;
                    this.fetching = false;
                    $Bus.$emit('messages:fetched', this.thread, this.messages);
                });
            },
        },
        components: {
            'message' : Message,
            'spinny' : Spinny
        }
    }
</script>
