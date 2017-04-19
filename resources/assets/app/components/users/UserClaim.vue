<template>
    <div>
        <div
            :class="claim.accepted ? null : ' b-l b-l-2x b-l-primary list-item-unread '"
            class="list-item b-b "
            :data-uuid="claim.uuid"
            :data-href="claim.view_route"
        >
            <div class="list-left">
                <div class="avatar-thumbnail-container">
                    <div v-if="claimerView" class="avatar-thumbnail _80">
                        <a :href="claim.view_route">
                            <img :src="claim.item.image">
                        </a>
                    </div>
                    <div v-else class="avatar-thumbnail _80">
                        <img :src="claim.item.image">
                    </div>
                </div>
            </div>
            <div class="list-body clearfix">
                <div v-if="claimerView">
                    <div class="pull-right">
                        <div class="text-right">
                            <a :href="claim.view_route" class="btn btn-xs white">View</a>
                            <cancel-claim
                                :claim="claim"
                                :claim-endpoint="claimEndpoint"
                                v-on:canceled="$emit('canceled')"
                            ></cancel-claim>
                        </div>
                    </div>
                    <a :href="claim.view_route">{{ claim.item.title }}</a>
                    <span>{{ claim.price }}</span>
                    <span class="text-sm" v-html="claim.shipping_status"></span>
                </div>
                <span v-else>{{ claim.item.title }}</span>
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
    import CancelClaim from '../claims/CancelClaim.vue';
    import Timestamp from '../Timestamp.vue';
    export default{
        props: {
            claim: {
                required: true,
                type: Object
            },
            claimEndpoint: {
                required: true,
                type: String
            },
            claimerView: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {}
        },
        components: {
            'cancel-claim': CancelClaim,
            'timestamp': Timestamp
        },
    }
</script>
