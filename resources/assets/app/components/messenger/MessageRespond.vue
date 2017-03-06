<template>
        <div class="input-group">
            <input type="text" v-model.trim="msg" class="form-control" @keydown.enter="storeResponse" :class="ready ? null : 'disabled'" :disabled="ready ? false : true" placeholder="Say something">
            <span class="input-group-btn">
                <button class="btn white" type="button" @click="storeResponse" :class="ready ? null : 'disabled'" :disabled="ready ? false : true">
                    Send
                    <spinny v-if="sending"></spinny>
                </button>
            </span>
        </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
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
                sending: false,
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
                if (this.msg == '' || !this.msg) {
                    return false;
                }
                this.sending = true;
                this.ready = false;
                this.$http.put(this.endpoint, {msg: this.msg}).then((response)=>{
                    this.msg = null;
                }, ()=>{}).finally(()=>{
                    this.sending = false;
                    this.ready = true;
                });
            },
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
