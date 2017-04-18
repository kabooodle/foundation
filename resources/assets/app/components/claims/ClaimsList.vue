<template>
    <div>
        <div class="list box-shadow-z0 white">
            <user-claim v-for="(claim, index) in claims" :key="claim.id"
                :claim="claim"
                :claim-endpoint="fetchEndpoint + '/' + claim.id"
                :claimer-view="claimerView"
                v-on:canceled="claims.splice(index, 1)"
            ></user-claim>
        </div>
        <infinite-loading :on-infinite="fetchInfinite" ref="claimsFinite">
            <span slot="no-more"></span>
            <span slot="no-results">
                No claims to view.
            </span>
            <span slot="spinner"><spinny class="text-center center-block m-t-1" :size="'' + 38"></spinny></span>
        </infinite-loading>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import UserClaim from '../users/UserClaim.vue';
    import InfiniteLoad from 'vue-infinite-loading';
    export default{
        props: {
            fetchEndpoint: {
                type: String,
                required: true,
            },
            claimerView: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return{
                fetching: false,
                filtering: false,
                pagination : {},
                claims : [],
            }
        },
        methods: {
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch claims directly, this allows us to set a few variables
             * that fetch claims will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                if (this.claims.length == 0 || ! this.claims.length) {
                    url = this.fetchEndpoint;
                }

                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchclaims(url);
                } else {
                    this.fetching = false;
                    this.$nextTick(()=>{
                        this.$refs.claimsFinite.$emit('$InfiniteLoading:complete');
                    });
                }
            },

            /**
             * Fetches all the claims using ajax.  This is called directly as well as with
             * query parameters used when filtering.
             *
             * @param url
             * @param data
             */
            fetchclaims(url, data){
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
                        this.claims.push(a);
                    });
                    this.$refs.claimsFinite.$emit('$InfiniteLoading:loaded');
                }

                this.makePagination(response.body.meta.pagination);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.claims.length >= this.pagination.total) {
                        this.$refs.claimsFinite.$emit('$InfiniteLoading:complete');
                    } else {
                        this.$refs.claimsFinite.$emit('$InfiniteLoading:loaded');
                    }
                });

                this.fetching = false;
            },

            /**
             * Make our pagination object, based on the fetch claims response.
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
            'user-claim' : UserClaim,
            InfiniteLoad
        }
    }
</script>
