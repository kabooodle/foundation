<template>
    <div>
        <spinny v-if="fetching" :size="'' + 28" class="text-center center-block" ></spinny>
        <div class="list white" v-if="listings">
            <user-listing
                    v-for="listing in listings"
                    :listing="listing"
                    :listing_endpoint="listing_endpoint.replace(/::UUID::/, listing.uuid)"
            ></user-listing>
        </div>
        <div v-if="!fetching && listings.length == 0">
            <p class="text-center">User has no scheduled upcoming listings.</p>
        </div>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import UserListing from './UserListing.vue';
    export default{
        props: {
            user: {
                required: true,
                type: Object
            },
            listings_endpoint: {
                required: true,
                type: String
            },
            listing_endpoint: {
                required: true,
                type: String
            },
        },
        data(){
            return{
                fetching: true,
                pagination: {},
                listings: []
            }
        },
        created(){
            this.fetchListings();
        },
        methods:{
            fetchListings(){
                this.fetching = true;
                this.$http.get(this.listings_endpoint).then((response)=>{
                    this.listings = response.body.data.data;
                    this.makePagination(response.body.data);
                    this.fetching = false;
                });
            },
            makePagination: function(data){
                let pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page_url: data.next_page_url,
                    prev_page_url: data.prev_page_url
                }
                this.pagination = pagination;
            }
        },
        components:{
            'spinny' : Spinny,
            'user-listing' : UserListing
        }
    }
</script>
