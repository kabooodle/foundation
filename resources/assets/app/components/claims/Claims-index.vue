<template>
    <tr
            v-for="claim in claims"
            :data-claim-id="claim.uuid" >
        <td >

        </td>
        <td>
            <a href=""
               class="_500 h6">
                <img>
            </a>
        </td>
        <td >${{ claim.price_usd }}</td>
        <td >{{ claim.claimer.name }}</td>
        <td >
            <timeago timestamp="{{ claim.created_on.date }}"></timeago>
        </td>
        <td class="action-column">
            <div class="pull-right">
                <a data-toggle="modal"
                   data-target="#modal_claim_rejected"
                   class="btn white btn-xs btn-action--rejected btn-action-claim"
                   @click="handleClaim('reject', claim, $event)"
                   data-route="{{ route('shop.claims.destroy', [$claim->inventoryItem->user->username, $claim->uuid]) }}">
                    Reject
                </a>
                <a data-toggle="modal"
                   data-target="#modal_claim_accepted"
                   class="btn white btn-xs  btn-action--accepted btn-action-claim"
                   @click="handleClaim('accept', claim, $event)"
                   data-route="{{ route('shop.claims.update', [$claim->inventoryItem->user->username, $claim->uuid]) }}" >
                    Accept
                </a>
            </div>
        </td>
    </tr>
</template>
<script>
    import Timeago from "./../Timestamp.vue";

    export default{
        props : {
            claims : {
                type: Array,
                default: {
                    return : []
                }
            }
        },
        data: function() {
            return {
                claim : {}
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