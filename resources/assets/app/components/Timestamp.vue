<template>
    <div v-if="older_than_week">
        {{ humanized }}
    </div>
    <div v-else>
        <timeago :since="timestamp"></timeago>
    </div>
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
                now: new Date().getTime(),
                format: 'MMM D \\at h:ma'
            }
        },
        computed : {
            older_than_week :function(){
                return moment(this.timestamp).subtract(1, 'weeks').isBefore(moment(this.timestamp));
            },
            humanized : function(){
                return moment(this.timestamp).format(this.format);
            },
        }
    }
</script>
