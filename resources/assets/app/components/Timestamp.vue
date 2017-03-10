<template>
    <span>
        <template v-if="older_than_week">
            {{ humanized }}
        </template>
        <template v-else>
            <timeago :since="rfc_timestamp"></timeago>
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
            rfc_timestamp(){
              return moment(this.timestamp).format('ddd, MM MMM YYYY HH:mm:ss ZZ');
            },
            older_than_week :function(){
                return moment(this.rfc_timestamp).isBefore(moment().subtract(1, 'weeks'));
            },
            humanized : function(){
                return moment(this.rfc_timestamp);
            },
        }
    }
</script>
