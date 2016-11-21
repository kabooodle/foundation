<template>
    <div>
        <div
                v-for="claim in claims"
                :data-claim-id="claim.uuid" >
            <td >

            </td>
            <td>
                <div class="avatar-thumbnail-container">
                    <div v-if="claim.inventory_item_object_data && claim.inventory_item_object_data.files[0]" class="avatar-thumbnail _32">
                        <img :src="claim.inventory_item_object_data.files[0].location">
                    </div>
                    <span>{{ claim.inventory_item_object_data.name_with_variant }}</span>
                </div>
            </td>
            <td >${{ claim.price_usd }}</td>
            <td >{{ claim.claimer.name }}</td>
            <td >
                <timeago :timestamp="claim.created_at.date"></timeago>
            </td>
            <td class="action-column">
                <div class="pull-right">
                    <!--<a data-toggle="modal"-->
                       <!--data-target="#modal_claim_rejected"-->
                       <!--class="btn white btn-xs btn-action&#45;&#45;rejected btn-action-claim"-->
                       <!--@click="handleClaim('reject', claim, $event)"-->
                    <!--&lt;!&ndash;data-route="{{ route('shop.claims.destroy', [$claim->inventoryItem->user->username, $claim->uuid]) }}">&ndash;&gt;-->
                    <!--Reject-->
                    <!--</a>-->
                    <!--<a data-toggle="modal"-->
                       <!--data-target="#modal_claim_accepted"-->
                       <!--class="btn white btn-xs  btn-action&#45;&#45;accepted btn-action-claim"-->
                       <!--@click="handleClaim('accept', claim, $event)"-->
                    <!--&lt;!&ndash;data-route="{{ route('shop.claims.update', [$claim->inventoryItem->user->username, $claim->uuid]) }}" >&ndash;&gt;-->
                    <!--Accept-->
                    <!--</a>-->
                </div>
            </td>
        </div>
    </div>
</template>
<script>
    import Timeago from "./../Timestamp.vue";

    export default{
        props : {
            claims : {
                type: Array,
            }
        },
        data: function() {
            return {
                selected_claim : {}
            }
        },
        methods : {
            handleClaim : function(choice, claim, event){
                event.preventDefault();
                this.claim = claim;
                switch(choice) {
                    case 'accept' :
                        return this.acceptClaim(claim);
                        break;
                    case 'reject' :
                        return this.rejectClaim(claim);
                        break;
                }
            },
            acceptClaim : function(claim){
                this.$http.put().then(function(response){

                }, function(response){
                    notify();
                });
            },
            rejectClaim : function(claim){
                this.$http.delete().then(function(response){

                }, function(response){
                    notify();
                });
            }
        },
        components : {
            'timeago' : Timeago
        }
    }
</script>