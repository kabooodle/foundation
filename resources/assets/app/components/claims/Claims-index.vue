<template>
    <tbody>
        <tr
                v-for="claim in all_claims"
                :data-claim-id="claim.uuid" >
            <td ></td>
            <td>
                <div class="avatar-thumbnail-container">
                    <div v-if="hasFiles(claim)" class="avatar-thumbnail _32">
                        <img :src="getFile(claim)">
                    </div>
                    <span>{{ claim.inventory_item_object_data.name_with_variant }}</span>
                </div>
            </td>
            <td >${{ claim.price }}</td>
            <td >{{ claim.claimer.name }}</td>
            <td >
                <timeago :timestamp="claim.created_at.date"></timeago>
            </td>
            <td class="action-column">
                <div class="pull-right action-btns">
                    <a data-toggle="modal"
                       data-target="#modal_claim_rejected"
                       class="btn white btn-xs btn-action--rejected btn-action-claim"
                       @click="handleClaim('reject', claim, $event)">
                    Reject
                    </a>
                    <a data-toggle="modal"
                       data-target="#modal_claim_accepted"
                       class="btn white btn-xs  btn-action--accepted btn-action-claim"
                       @click="handleClaim('accept', claim, $event)">
                    Accept
                    </a>
                </div>
            </td>
        </tr>
    </tbody>
</template>
<script>
    import Timeago from "./../Timestamp.vue";

    export default{
        props : {
            claims : {
                type: Array,
                default : {
                    return : []
                }
            }
        },
        data: function() {
            return {
                all_claims: [],
                selected_claim : {}
            }
        },
        created : function(){
            this.all_claims = this.claims;
        },
        methods : {
            hasFiles : function(claim) {
                return claim.inventory_item_object_data.files && claim.inventory_item_object_data.files.length;
            },
            getFile : function(claim) {
                return this.hasFiles(claim) ? claim.inventory_item_object_data.files[0].location : null;
            },
            handleClaim : function(choice, claim, event){
                event.preventDefault();
                this.selected_claim = claim;
                let route = window.location+'/'+claim.uuid;
                switch(choice) {
                    case 'accept' :
                        return this.acceptClaim(route,claim);
                        break;
                    case 'reject' :
                        return this.rejectClaim(route,claim);
                        break;
                }
            },
            acceptClaim : function(route,claim){
                const scope = this;
                this.$http.put(route,{claim: claim.id}).then(function(response){
                    scope.removeClaim(claim);
                }, function(response){
                    notify(response.body.data.msg);
                });
            },
            rejectClaim : function(route,claim){
                const scope = this;
                this.$http.delete(route, {claim: claim.id}).then(function(response){
                    scope.removeClaim(claim);
                }, function(response){
                    notify(response.body.data.msg);
                });
            },
            removeClaim : function(claim){
                let index = this.all_claims.indexOf(claim);
                if(index > -1){
                    this.all_claims.splice(index, 1);
                }
            },
        },
        components : {
            'timeago' : Timeago
        }
    }
</script>