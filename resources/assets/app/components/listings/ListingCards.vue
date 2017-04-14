<template>
    <div>
            <listing-card
                    v-for="item in items"
                    :key="item.id"
                    :item="item"
                    :listed_item="item.listed_item"
                    :watch_endpoint="watch_endpoint.replace(/::1::/, item.listing_id).replace(/::2::/, item.id)"
                    :show_endpoint="show_endpoint.replace(/::1::/, item.id_to_string)"
            ></listing-card>
            <infinite-loading :on-infinite="fetchInfinite" ref="listingFinite">
                <span slot="no-more"></span>
                <span slot="no-results">
                    No listing items found.
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
        },
        methods:{
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch items directly, this allows us to set a few variables
             * that fetch items will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                if (this.items.length == 0 || ! this.items.length) {
                    url = this.fetch_endpoint;
                }

                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchItems(url);
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
                    this.items = response.body.data;
                    $Bus.$emit('listing-filter:completed', this.items);
                } else {
                    if (response.body.data.length){
                        _.each(response.body.data, (a)=>{
                            this.items.push(a);
                        });
                        this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                    }
                }

                this.makePagination(response.body.meta.pagination);
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
                    last_page: data.current_page > 1 ? data.current_page - 1 : null,
                    next_page_url: data.links.next,
                    prev_page_url: data.links.previous,
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
