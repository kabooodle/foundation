<template>
    <div>
        <div
                class="list-item b-b"
                :data-uuid="listing.uuid"
                :data-href="listing.endpoint"
        >
            <div class="list-left">
                <img :src="listing.type_icon_src">
            </div>
            <div class="list-body clearfix">
                <div class="pull-right">
                    <div class="text-right">
                        <a :href="listing.endpoint" class="btn btn-xs white">View</a>
                    </div>
                </div>
                <a :href="listing.endpoint" class="_500 block">{{ listing.sale_name }}</a>
                <span class="text-sm text-muted block">Available for claims {{ normalizedScheduleRange }} <span class="m-l-1">Items listed, {{ listing.items_count }}</span></span>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props: {
            listing: {
                required: true,
                type: Object
            },
            listing_endpoint: {
                required: true,
                type: String
            }
        },
        data(){
            return{
                date_format: 'MMM D \\at h:mma'
            }
        },
        computed: {
            normalizedScheduleRange(){
                return this.listing.claimable_range;
            }
        },
    }
</script>
