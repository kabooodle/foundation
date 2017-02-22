<template>
    <tbody>
        <tr
                v-for="claim in all_claims"
                :data-claim-id="claim.uuid" >
            <!--<td><input-->
                        <!--@change="selectedClaimsChanged(claim, $event)"-->
                        <!--type="checkbox"-->
                        <!--class="claim_checks"-->
                        <!--:data-id="claim.id"-->
                        <!--name="claims[]"-->
                        <!--:value="claim.id">-->
            <!--</td>-->
            <td>
                <div class="avatar-thumbnail-container">
                    <div class="avatar-thumbnail _32">
                        <img :src="getFile(claim)">
                    </div>
                    <span>{{ claim.listable_item_object_data.name_with_variant }}</span>
                </div>
            </td>
            <td >${{ claim.price }}</td>
            <td >
                {{ claim.claimer.username }}
                <small v-if="claim.claimer.guest" class="text-muted">Guest</small>
            </td>
            <td >
                <timeago :timestamp="claim.created_at"></timeago>
            </td>
            <td>
                <span v-if="claim.verified">
                    <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                </span>
                <span v-else>
                    <small class="text-muted">Pending</small>
                </span>
            </td>
            <td class="action-column">
                <div class="pull-right action-btns">
                    <a
                       class="btn white btn-xs btn-action--rejected btn-action-claim"
                       @click="handleClaim('reject', claim, $event)">
                    Reject
                    </a>
                    <a
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
                selected_claim_route : '',
                selected_claim : {},
                selected_claims: [],
            }
        },
        created : function(){
            const scope = this;
            this.all_claims = this.claims;
            this.$nextTick(function(){
                $('.datetimepicker').datetimepicker({
                    maxDate: moment().add(1, 'days'),
                    format: "MM/DD/YYYY hh:mmA",
                    icons: {
                        time: 'fa fa-clock-o',
                        date: "fa fa-calendar",
                        up: 'fa fa-chevron-up',
                        down: 'fa fa-chevron-down',
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right'
                    }
                });
            });

            $Bus.$on('selected-claim:handled', function(){
                scope.removeClaim(scope.selected_claim);
            });
        },
        methods : {
            selectedClaimsChanged : function(claim, event){
                event.preventDefault();
                const el = event.target;
                console.log('hi');
                if(el.checked) {
                    this.addToSelectedClaims(claim);
                } else {
                    this.removeFromSelectedClaims(claim);
                }
            },
            addToSelectedClaims : function(claim){
                this.selected_claims.push(claim);
            },
            removeFromSelectedClaims: function(claim){
                let index = this.selected_claims.indexOf(claim);
                this.selected_claims.splice(index, 1);
            },
            getFile : function(claim) {
                return claim.listable_item_object_data.cover_photo.location;
            },
            handleClaim : function(choice, claim, event){
                event.preventDefault();
                this.selected_claim = claim;
                this.selected_claim_route = window.location+'/'+claim.uuid;
                switch(choice) {
                    case 'accept' :
                        return this.toggleAcceptClaimModal();
                        break;
                    case 'reject' :
                        return this.toggleRejectClaimModal();
                        break;
                }
            },
            toggleAcceptClaimModal : function(){
                const scope = this;
                const modal = $('#modal_claim_accepted');
                const form = modal.find('form');

                form.prop('action', this.selected_claim_route);
                modal.find('input[name="accepted_price"]').val(this.selected_claim.price);
                modal.find('input[name="accepted_on"]').val(moment(this.selected_claim.created_at.date).format('MM/DD/YYYY hh:mmA'));
                modal.modal('show');
            },
            toggleRejectClaimModal : function(){
                const scope = this;
                const modal = $('#modal_claim_rejected');
                const form = modal.find('form');

                form.prop('action', this.selected_claim_route);
                modal.modal('show');
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