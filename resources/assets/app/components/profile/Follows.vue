<template>
    <div class="row">
        <div class="col-sm-6"
             v-for="item in items"
             :key="item.id"
        >
            <vcard
                    :able_id="item.id+''"
                    :already_following="item.already_following ? 1 : 0"
                    :follow_endpoint="item.follow_endpoint"
                    :message_endpoint="message_endpoint"
                    :user="item"
                    able_type="Kabooodle\Models\User"
            ></vcard>
        </div>
        <infinite-loading :on-infinite="fetchInfinite" ref="listingFinite">
            <span slot="no-more"></span>
            <span slot="no-results">
                <span>{{ no_results_text }}</span>
            </span>
            <span slot="spinner">
                <spinny class="text-center center-block" :size="'' + 38"></spinny>
            </span>
        </infinite-loading>
    </div>
</template>
<script>
    import currentUser from '../current-user';
    import InfiniteScroll from 'vue-infinite-loading';
    import VCard from '../users/V-Card.vue';
    import Spinny from  '../Spinner.vue';

    export default {
        props: {
            no_results_text: {
                required: true,
                type: String
            },
            message_endpoint: {
                type: String,
                required: true,
            },
            items_endpoint: {
                type: String,
                required: true,
            }
        },
        data(){
            return{
                actions: {
                    fetching: false,
                },
                pagination : {},
                infinite: false,
                items: []
            }
        },
        created(){
            this.fetchItems(this.items_endpoint);
        },
        methods:{

            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch items directly, this allows us to set a few variables
             * that fetch items will check.
             */
            fetchInfinite(){
                if (this.actions.fetching) {
                    return;
                }

                let url = this.pagination.next_page_url;

                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchItems(this.pagination.next_page_url);
                } else {
                    this.actions.fetching = false;
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
             */
            fetchItems(url){
                if (this.actions.fetching) {
                    return;
                }

                this.actions.fetching = true;
                this.$http.get(url).then((response)=>{
                    this.handleResponse(response);
                }, (response)=>{});
            },

            /**
             *
             * @param  objectresponse
             */
            handleResponse(response){
                if(response.body.data.length > 0){
                    this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                    _.each(response.body.data, (a)=>{
                        this.items.push(a);
                    });
                }

                this.makePagination(response.body.meta.pagination);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.items.length >= this.pagination.total) {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                    } else {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                    }
                });

                this.actions.fetching = false;
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
        components:{
            InfiniteScroll,
            'vcard' : VCard,
            'spinny' : Spinny
        }
    }
</script>
