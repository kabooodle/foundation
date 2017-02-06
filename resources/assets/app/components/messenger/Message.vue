<template>
    <div>
        <div
                class="m-b"
                :class="author_is_self ? 'm-l-3' : 'm-r-3'"
        >
            <div
                    class="clear"
                    :class="author_is_self ? 'text-right' : ''"
            >
                <div>
                    <div
                            :class="author_is_self ? 'primary' : 'grey-200 b-a'"
                            class="p-a p-y-sm inline  r"
                    ><span class="message_body" v-html="message.body"></span></div>
                </div>
                <timestamp :timestamp="message.created_at" class="text-muted text-xs m-t-xs"></timestamp>
            </div>
        </div>
    </div>
</template>
<style>
    .message_body p {
        margin: 0;
        padding: 0;
    }
</style>
<script>
    import currentUser from '../current-user';
    import Timestamp from '../Timestamp.vue';
    export default{
        props: {
            message: {
                required: true,
                type: Object
            }
        },
        computed : {
            author_is_self(){
                return parseInt(this.message.user_id) == parseInt(currentUser().id);
            },
        },
        components:{
            'timestamp' : Timestamp
        }
    }
</script>
