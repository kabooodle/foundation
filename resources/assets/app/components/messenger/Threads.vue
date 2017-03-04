<template>
    <div>
        <spinny v-if="fetching" :size="'' + 28" class="text-center center-block" ></spinny>
        <div class="list white" v-if="threads">
            <thread
                    v-for="thread in threads"
                    :key="thread.id"
                    :thread="thread"
                    :endpoint="thread_endpoint.replace(/::ID::/, thread.id)"
            ></thread>
        </div>
        <div v-if="!fetching && threads.length == 0">
            <p class="text-center">Your inbox is empty.</p>
        </div>
    </div>
</template>s
<script>
    import Spinny from '../Spinner.vue';
    import Thread from './Thread.vue';
    export default{
        props: {
            endpoint: {
                required: true,
                type: String
            },
            thread_endpoint : {
                required: true,
                type: String
            }
        },
        data(){
            return{
                threads: [],
                pagination : {},
                fetching: false
            }
        },
        mounted() {
            this.fetchThreads();
        },
        methods: {
            fetchThreads : function(url){
                url = url || this.endpoint;
                this.fetching = true;
                this.$http.get(url).then((response)=>{
                    this.threads = response.body.data.data;
                    this.makePagination(response.body.data);
                    this.fetching = false;
                });
            },
            makePagination: function(data){
                let pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page_url: data.next_page_url,
                    prev_page_url: data.prev_page_url
                }
                this.pagination = pagination;
            }
        },
        components:{
            'thread' : Thread,
            'spinny' : Spinny
        }
    }
</script>
