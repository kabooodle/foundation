<template>
    <div>
        <div id="inline_message" class="m-b-sm"></div>
        <div v-show="claimType === null || claimType === 'user'">
            <p class="text-center">
                <a @click="claimType = 'user'" class="text-primary">Claim For User</a>
                    <span v-show="claimType === 'user'">
                        or
                        <a @click="claimType = null" class=text-muted>Go Back</a>
                    </span>
            </p>
            <div v-show="claimType === 'user'">
                <div class="form-group-row">
                    <user-search
                            :search_endpoint=search_endpoint
                    ></user-search>
                </div>
                <div class="form-group-row">
                    <button type="button" class="btn primary btn-block p-x-md" @click="claimForUser" :disabled="!userClaimer || loading === true">Confirm Claim!<spinny v-if="loading"></spinny></button>
                </div>
            </div>
        </div>
        <div v-show="claimType === null || claimType === 'guest'">
            <p class="text-center">
                <a @click="claimType = 'guest'" class="text-primary">Claim For Guest</a>
                    <span v-show="claimType === 'guest'">
                        or
                        <a @click="claimType = null" class=text-muted>Go Back</a>
                    </span>
            </p>
            <div v-show="claimType === 'guest'">
                <guest-claim
                    :claim-endpoint=endpoint
                    :convert-endpoint=convertEndpoint
                    :csrf=csrf
                    :owner-claim=true
                ></guest-claim>
            </div>
        </div>
    </div>
</template>
<script>
    import GuestClaim from './GuestClaim.vue'
    import UserSearch from '../users/UserSearch.vue'
    import Spinny from '../Spinner.vue';
    export default{
        props:{
            search_endpoint: {
                type: String,
                required: true
            },
            endpoint: {
                type: String,
                required: true
            },
            claimRedirect: {
                type: String,
                required: true
            },
            convertEndpoint: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
        },
        data(){
            return{
                userClaimer: null,
                nonUserClaimer: null,
                loading: false,
                claimSuccess: true,
                claimType: null
            }
        },

        methods:{
            claimForUser(){
                this.loading = true;

                this.$http.post(this.endpoint, {
                    claimer_id: this.userClaimer.id
                }).then((response)=>{
                    this.claimSuccess = true;
                    notify({
                        'el': $('#inline_message'),
                        'text': 'The item has been claimed!',
                        'type': 'success'
                    });
                    setTimeout(function(){
                        location.reload();
                    }, 1800)
                }, (response)=>{
                    this.claimSuccess = false;
                     notify({
                         'el': $('#inline_message'),
                        'text': response.body.msg,
                        'type': 'error'
                     });
                }).finally(()=>{
                    this.loading = false;
                });
            }
        },

        components:{
            'guest-claim':GuestClaim,
            'user-search':UserSearch,
            'spinny' :Spinny
        }
    }
</script>
