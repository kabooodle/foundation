<template>
    <div>
        <div
                class="list-item b-l b-l-2x b-b"
                :data-id="thread.id"
                :data-href="endpoint"
        >
                <!--<div class="list-left">-->
                <!--</div>-->
                <div class="list-body">
                    <div class="pull-right text-muted">
                        <div class="text-sm text-right">{{ thread.participants_names_excluding_creator}}</div>
                        <div class="text-right text-muted text-sm"><timestamp :timestamp="most_recent_message.created_at.date"></timestamp></div>
                    </div>
                    <a :href="endpoint" class="_500 block">{{ thread.subject }}</a>
                    <div class="text-ellipsis text-muted text-sm p-r-3">
                        <span>{{ most_recent_message.body }}</span>
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
            most_recent_message(){
                return _.last(this.thread.messages)
            },
        },
        components: {
            'timestamp' : Timestamp
        },
    }
</script>
