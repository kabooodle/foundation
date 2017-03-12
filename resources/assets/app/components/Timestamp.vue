<template>
    <span>
        <template v-if="older_than_week">
            {{ humanized }}
        </template>
        <template v-else>
            <timeago :since="iso_timestamp"></timeago>
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
            iso_timestamp(){
              return moment(this.timestamp).format();
            },
            older_than_week :function(){
                return moment(this.iso_timestamp).isBefore(moment().subtract(1, 'weeks'));
            },
            humanized : function(){
                return moment(this.iso_timestamp).format(this.format);
            },
        }
    }
</script>
