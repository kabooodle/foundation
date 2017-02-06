<template>
    <div>
        <spinny v-if="fetching" :size="'' + 28" class="text-center center-block" ></spinny>
            <listing-card
                    v-for="item in items"
                    :key="item.id"
                    :item="item"
                    :inventory_item="item.inventory_item"
                    :watch_endpoint="watch_endpoint.replace(/::1::/, item.listing_id).replace(/::2::/, item.id)"
                    :show_endpoint="show_endpoint.replace(/::1::/, item.id_to_string)"
            ></listing-card>
            <infinite-loading :distance="100" :on-infinite="fetchInfinite" ref="listingFinite">
                <span slot="no-more">
                    No more results.
                </span>
                <span slot="no-results">
                    No more results.
                </span>
                <span slot="spinner">
                    <spinny class="text-center center-block" :size="'' + 38"></spinny>
                </span>
            </infinite-loading>
    </div>
</template>
<script>
    import ListingCard from './ListingCard.vue';
    import Spinner from '../Spinner.vue';
    import InfiniteLoading from 'vue-infinite-loading';
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            },
            watch_endpoint: {
                required: true,
                type: String
            },
            show_endpoint: {
                required: true,
                type: String
            },
        },
        data(){
            return{
                infinite: false,
                pagination : {},
                items : [],
                fetching: false,
                filtering: false,
                filters: {
                    styles: [],
                    sizes: [],
                    sellers: [],
                }
            }
        },
        created(){
            $Bus.$on('listing-filter:request', (styles, sizes, sellers)=>{
                this.filtering = true;
                this.filters.styles = styles;
                this.filters.sizes = sizes;
                this.filters.sellers = sellers;
                this.items = [];
                this.fetchItems(this.fetch_endpoint, {styles: styles, sizes: sizes, sellers: sellers});
            });

            this.fetchItems(this.fetch_endpoint);
        },
        methods:{
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch items directly, this allows us to set a few variables
             * that fetch items will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchItems(this.pagination.next_page_url);
                } else {
                    this.fetching = false;
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
                    this.items = response.body.data.data;
                    $Bus.$emit('listing-filter:completed', this.items);
                } else {
                    _.each(response.body.data.data, (a)=>{
                        this.items.push(a);
                    });
                }

                this.makePagination(response.body.data);
                this.filtering = false;

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.items.length >= this.pagination.total) {
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
        components: {
            'spinny' : Spinner,
            'listing-card' : ListingCard,
            InfiniteLoading,
        }
    }
</script>
