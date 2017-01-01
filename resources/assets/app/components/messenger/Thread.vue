<template>
    <div>
        <div
                class="list-item b-l b-l-2x b-b"
                :data-id="thread.id"
                :data-href="endpoint"
        >
                <div class="list-left">
                    <span class="w-40 avatar">
                        <img src="../assets/images/a0.jpg">
                    </span>
                </div>
                <div class="list-body">
                    <div class="pull-right text-muted text-xs">
                        <span class="text-muted text-sm">{{ thread.participants_names_excluding_creator}}</span>
                        <timestamp :timestamp="thread.created_at.date"></timestamp>
                    </div>
                    <a :href="endpoint" class="_500 block">{{ thread.subject }}</a>
                    <div class="text-ellipsis text-muted text-sm p-r-3">
                        <span>{{ _.last(thread.messages).body }}</span>
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
        components: {
            'timestamp' : Timestamp
        },
    }
</script>
