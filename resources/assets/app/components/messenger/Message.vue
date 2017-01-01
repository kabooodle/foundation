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
                    >{{ message.body }}</div>
                </div>
                <timestamp :timestamp="message.created_at.date" class="text-muted text-xs m-t-xs"></timestamp>
            </div>
        </div>
    </div>
</template>
<script>
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
                return parseInt(this.message.user.id) == parseInt(KABOOODLE_APP.currentUser.id);
            },
        },
        components:{
            'timestamp' : Timestamp
        }
    }
</script>
