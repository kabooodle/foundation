<template>
    <div>
            <div
                    v-for="flashsale in flashsales"
                    :key="flashsale.id"
                    class="col-md-3"
            >
                <flashsale-card
                        :flashsale="flashsale"
                        :show_endpoint="show_endpoint.replace(/::0::/, flashsale.uuid)"
                ></flashsale-card>
            </div>
            <infinite-loading :on-infinite="fetchInfinite" ref="listingFinite">
                <span slot="no-more"><span v-if="!fetching">End results.</span></span>
                <span slot="no-results"><span v-if="!fetching">End results.</span></span>
                <span slot="spinner"><spinny class="text-center center-block" :size="'' + 38"></spinny></span>
            </infinite-loading>
    </div>
</template>
<script>
    import FlashsaleCard from './Flashsale-card.vue';
    import InfiniteLoading from 'vue-infinite-loading';
    import Spinny from  '../Spinner.vue';
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            },
//            watch_endpoint: {
//                required: true,
//                type: String
//            },
            show_endpoint: {
                required: true,
                type: String
            },
        },
        data(){
            return{
                flashsales: [],
                fetching: false,
                filtering: false,
                pagination : {},
            }
        },
        created(){
            $Bus.$on('flashsales:filter', (salename, sellers)=>{
                this.filtering = true;
                this.flashsales = [];
                this.fetchItems(this.fetch_endpoint, {name: salename, sellers: sellers});
            });
        },
        methods: {
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch items directly, this allows us to set a few variables
             * that fetch items will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                if (this.flashsales.length == 0 || ! this.flashsales.length) {
                    url = this.fetch_endpoint;
                }


                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchItems(url);
                } else {
                    this.$nextTick(()=>{
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                    });
                }
            },

            /**
             * Fetches all the items using ajax.  This is called directly as well as with
             * query parameters used when filtering.
             *
             * @param url
             * @param data
             */
            fetchItems(url, data){
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
                if (this.filtering) {
                    this.flashsales = response.body.data;
                    $Bus.$emit('listing-filter:completed', this.flashsales);
                } else {
                    _.each(response.body.data, (a)=>{
                        this.flashsales.push(a);
                    });
                }

                this.makePagination(response.body.meta.pagination);
                this.filtering = false;
                $Bus.$emit('fetch:completed', this.flashsales);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.flashsales.length >= this.pagination.total) {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                    } else {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                    }
                });

                this.fetching = false;
            },

            /**
             * Make our pagination object, based on the fetch items response.
             * @param data
             */
            makePagination: function(data){
                const pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page_url: data.next_page_url,
                    prev_page_url: data.prev_page_url,
                    total: data.total,
                    to: data.to
                }
                this.pagination = pagination;
            }
        },
        components:{
            'spinny' : Spinny,
            'flashsale-card' : FlashsaleCard,
            InfiniteLoading,
        }
    }
</script>
