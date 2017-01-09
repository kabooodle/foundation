<template>
    <div>
        <div
                class="list-item b-l b-l-2x b-b"
                :data-id="thread.id"
                :data-href="endpoint"
                :class="is_read ? null : ' b-l-primary list-item-unread '"
        >
                <!--<div class="list-left">-->
                <!--</div>-->
                <div class="list-body">
                    <div class="pull-right text-muted">
                        <div class="text-sm text-right">{{ thread.participants_names_excluding_creator}}</div>
                        <div class="text-right text-muted text-sm"><timestamp :timestamp="most_recent_message.created_at"></timestamp></div>
                    </div>
                    <a :href="endpoint" class="_500 block">{{ thread.subject }}</a>
                    <div class="text-ellipsis text-muted text-sm p-r-3">
                        <span v-html="most_recent_message.body"></span>
                    </div>
                </div>
        </div>
    </div>
</template>
<script>
    import Timestamp from '../Timestamp.vue';
    export default{
        props: {
            thread: {
                required: true,
                type: Object
            },
            endpoint : {
                required: true,
                type: String
            }
        },
        computed:{
            is_read(){
                const thread_updated_at = this.thread.updated_at;
                const myself = _.find(this.thread.participants, function(v){  return parseInt(v.user_id) == parseInt(KABOOODLE_APP.currentUser.id);  });

                if (_.has(myself.last_read, 'date')) {
                    return moment(myself.last_read.date).isAfter(thread_updated_at.date);
                }

                return false;
            },
            most_recent_message(){
                return _.last(this.thread.messages)
            },
        },
        components: {
            'timestamp' : Timestamp
        },
    }
</script>
