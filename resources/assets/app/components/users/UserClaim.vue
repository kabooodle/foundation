<template>
    <div>
        <div
            class="list-item b-b"
            :data-uuid="claim.uuid"
            :data-href="claim.view_route"
        >
            <div class="list-left">
                <div class="avatar-thumbnail-container">
                    <div class="avatar-thumbnail _56">
                        <img :src="claim.item.image">
                    </div>
                </div>
            </div>
            <div class="list-body clearfix">
                <div class="pull-right">
                    <div class="text-right">
                        <a :href="claim.view_route" class="btn btn-xs white">View</a>
                    </div>
                </div>
                <a :href="claim.view_route">{{ claim.item.title }}</a>
                <span>{{ claim.price }}</span>
                <span class="text-sm" v-html="claim.shipping_status"></span>
                <span class="text-sm text-muted block"><timestamp :timestamp="claim.created_at.date"></timestamp></span>
                <span class="text-sm text-muted block">Sale: <a :href="claim.listing.view_route" class="text-primary">{{ claim.listing.name }}</a></span>
                <span class="text-sm text-muted block">Seller: <a :href="claim.item.owner.view_route" class="text-primary">{{ claim.item.owner.username }}</a></span>
            </div>
        </div>
    </div>
</template>
<style>
    .list-left + .list-body {
        margin-left: 72px;
    }
</style>
<script>
    import Timestamp from '../Timestamp.vue';
    export default{
        props: {
            claim: {
                required: true,
                type: Object
            },
            claim_endpoint: {
                required: true,
                type: String
            }
        },
        data(){
            return {

            }
        },
        components: {
            'timestamp': Timestamp,
        },
    }
</script>
