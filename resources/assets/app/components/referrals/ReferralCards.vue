<template>
    <div class="body white p-a m-t-2">
        <spinny class="text-center center center-block m-a" :size="'64'" v-if="actions.fetching"></spinny>
        <template v-else>
            <template v-if="referrals.length">
                <div class="row">
                    <div
                            class="col-md-4"
                            v-for="referral in referrals"
                            :key="referral.id"
                    >
                        <v-card
                                :outer_class="referral.is_qualified ? ' b-a brdr-prpl ' : null"
                                :user="referral"
                                :able_id="'referral.id'"
                                able_type="Kabooodle\Models\Users"
                                :message_endpoint="message_endpoint"
                                :follow_endpoint="referral.follow_endpoint"
                                :already_following="referral.already_following ? 1 : 0"
                        ></v-card>
                        <div
                                class="text-xs clearfix p-a-xs"
                                :class="referral.is_qualified ? ' primary ' : ' b-l b-r b-b white '"
                        >
                            <span class="pull-left" v-if="referral.is_qualified">
                                Qualified <timeago :timestamp="referral.qualified_on.date"></timeago>
                            </span>
                            <span class="pull-right">
                                Joined <timeago :timestamp="referral.joined_on.date"></timeago>
                            </span>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <slot></slot>
            </template>
        </template>
    </div>
</template>
<script>
    import Timeago from "./../Timestamp.vue";
    import currentUser from '../current-user';
    import VCard from '../users/V-Card.vue';
    import Spinny from '../Spinner.vue';
    export default{
        props: {
            message_endpoint: {
                required: true,
                type: String,
            },
            referrals_endpoint: {
                required: true,
                type: String
            }
        },
        data(){
            return{
                actions: {
                    fetching: true,
                },
                referrals: [],
                current_user: currentUser(),
            }
        },
        mounted() {
            this.getReferrals();
        },
        methods: {
            getReferrals(){
                this.actions.fetching = true;
                this.$http.get(this.referrals_endpoint).then((response)=>{
                    this.referrals = response.body.data;
                }, (response)=>{}).finally(()=>{
                    this.actions.fetching = false;
                });
            },
        },
        components:{
            'v-card' : VCard,
            'spinny' : Spinny,
            'timeago' : Timeago
        }
    }
</script>
