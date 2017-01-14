<template>
    <div>
        <div
                class="list-item b-l b-l-2x b-b"
                :data-uuid="listing.uuid"
                :data-href="listing_endpoint"
        >
            <div class="list-left">
                <img :src="listing.type_icon_src">
            </div>
            <div class="list-body clearfix">
                <div class="pull-right">
                    <div class="text-right">
                        <button class="btn btn-xs white">Follow</button>
                        <a :href="listing_endpoint" class="btn btn-xs white">View</a>
                    </div>
                </div>
                <a :href="listing_endpoint" class="_500 block">{{ listing.sale_name }}</a>
                <span class="text-sm text-muted block">Available for claims {{ normalizedScheduleRange }} <span class="m-l-1">Items listed, {{ listing.items.length }}</span></span>
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
                msg:'hello vue',
                date_format: 'MMM D \\at h:mma'
            }
        },
        computed: {
            normalizedScheduleRange(){
                let starts_at = this.listing.scheduled_for;
                let ends_at = this.listing.scheduled_until;
                let claimable_at = this.listing.claimable_at;
                let claimable_until = this.listing.claimable_until;

                let scheduled = claimable_at ? claimable_at : starts_at;
                let scheduled_end = claimable_until ? claimable_until : (ends_at ? ends_at : null);

                return scheduled_end ? moment(scheduled).format(this.date_format)+' - '+moment(scheduled_end).format(this.date_format) : moment(scheduled).format(this.date_format);
            }
        },
    }
</script>
