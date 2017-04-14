<template>
    <div>
        <div class="list box-shadow-z0 white" v-if="listings">
            <user-listing
                    v-for="listing in listings"
                    :key="listing.id"
                    :listing="listing"
                    listing_endpoint=""
            ></user-listing>
        </div>
        <infinite-loading :on-infinite="fetchInfinite" ref="listingFinite">
            <span slot="no-more"></span>
            <span slot="no-results">
                No scheduled listings to view.
            </span>
            <span slot="spinner">
                <spinny class="text-center center-block" :size="'' + 38"></spinny>
            </span>
        </infinite-loading>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import UserListing from '../users/UserListing.vue';
    import InfiniteLoad from 'vue-infinite-loading';
    export default{
        props: {
            fetch_endpoint: {
                type: String,
                required: true,
            }
        },
        data(){
            return{
                fetching: false,
                filtering: false,
                pagination : {},
                listings : [],
            }
        },
        methods: {
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch listings directly, this allows us to set a few variables
             * that fetch listings will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                if (this.listings.length == 0 || ! this.listings.length) {
                    url = this.fetch_endpoint;
                }

                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchlistings(url);
                } else {
                    this.fetching = false;
                    this.$nextTick(()=>{
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                    });
                }
            },

            /**
             * Fetches all the listings using ajax.  This is called directly as well as with
             * query parameters used when filtering.
             *
             * @param url
             * @param data
             */
            fetchlistings(url, data){
                if (this.fetching) {
                    return;
                }

                this.fetching = true;
                this.$http.get(url, {params: data}).then((response)=>{
                    this.handleResponse(response);
                });
            },

            /**
             *
             * @param  objectresponse
             */
            handleResponse(response){
                if (response.body.data.length) {
                    _.each(response.body.data, (a)=>{
                        this.listings.push(a);
                    });
                    this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                }

                this.makePagination(response.body.meta.pagination);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.listings.length >= this.pagination.total) {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                    } else {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                    }
                });

                this.fetching = false;
            },

            /**
             * Make our pagination object, based on the fetch listings response.
             * @param data
             */
            makePagination: function(data){
                const pagination = {
                    current_page: data.current_page,
                    last_page: data.current_page > 1 ? data.current_page - 1 : null,
                    next_page_url: data.links.next,
                    prev_page_url: data.links.previous,
                    total: data.total,
                    to: data.to
                }
                this.pagination = pagination;
            }
        },
        components:{
            'spinny' : Spinny,
            'user-listing' : UserListing,
            InfiniteLoad
        }
    }
</script>
