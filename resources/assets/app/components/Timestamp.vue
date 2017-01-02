<template>
    <span>
        <template v-if="older_than_week">
            {{ humanized }}
        </template>
        <template v-else>
            <timeago :since="timestamp"></timeago>
        </template>
    </span>
</template>
<script>
    export default{
        props: {
            timestamp: {
                required: true
            },
        },
        data(){
            return {
                format: 'MMM D \\at h:mma'
            }
        },
        computed : {
            older_than_week :function(){
                return moment(this.timestamp).isBefore(moment().subtract(1, 'weeks'));
            },
            humanized : function(){
                return moment(this.timestamp).format(this.format);
            },
        }
    }
</script>
