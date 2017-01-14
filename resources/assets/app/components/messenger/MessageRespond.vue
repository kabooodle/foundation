<template>
        <div class="input-group">
            <input type="text" v-model.trim="msg" class="form-control" @keydown.enter="storeResponse" :class="ready ? null : 'disabled'" :disabled="ready ? false : true" placeholder="Say something">
            <span class="input-group-btn">
                <button class="btn white" type="button" @click="storeResponse" :class="ready ? null : 'disabled'" :disabled="ready ? false : true">Send</button>
            </span>
        </div>
</template>
<script>
    export default{
        props: {
            endpoint: {
                required: true,
                type: String
            }
        },
        data(){
            return{
                msg: null,
                ready: false,
                storing: false,
                typing: false,
            }
        },
        created(){
            $Bus.$on('messages:fetched', (messages, thread)=>{
                this.ready = true;
            });
        },
        methods: {
            storeResponse(event){
                if (this.msg == '') {
                    return false;
                }
                this.sending = true;
                this.ready = false;
                this.$http.put(this.endpoint, {msg: this.msg}).then((response)=>{
                    this.sending = false;
                    this.ready = true;
                    this.msg = null;
                });
            },
        },
    }
</script>
