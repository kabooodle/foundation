<template>
    <div>
        <template v-if="toggled.length">
            <div id="claims_actions_wrapper"
            :class="toggledHasPending ? null : ' claims_actions_mini '"
            >
                <div class="actions-menu">
                            <a
                                    v-if="toggledHasPending"
                                    class="btn btn-sm white"
                                    @click="bulkAccept"
                               data-toggle="dropdown"
                               href="#assignMenu"
                               rel="tooltip"
                               data-placement="bottom"
                               title="abc"
                               data-original-title="Assign (a)">
                                Accept
                            </a>

                            <a class="btn btn-sm white"
                               data-toggle="dropdown"
                               href="#assignMenu"
                               rel="tooltip"
                               data-placement="bottom"
                               @click="bulkReturn"
                               title="abc"
                               data-original-title="Assign (a)">
                                Return
                            </a>
                            <a class="btn btn-sm white"
                               data-toggle="dropdown"
                               href="#assignMenu"
                               rel="tooltip"
                               data-placement="bottom"
                               @click="bulkTag"
                               title="abc"
                               data-original-title="Assign (a)">
                                Add Tag(s)
                            </a>
                    <div class="block text-muted text-center text-xs m-t-xs">
                        {{ toggled.length }} Claims Selected
                    </div>
                </div>
            </div>
        </template>
        <div class="list box-shadow-z0 white">
            <div
                    v-for="claim in claims"
                    :key="claim.id"
                    :data-claim-id="claim.uuid"
                    :class="claim.is_guest ? ' b-l b-l-2x b-l-primary list-item-unread ' : null"
                    class="list-item b-b ">
                <div class="list-left" :data-id="claim.id">
                    <h4 style="margin-top: 10px;"
                        @click="toggleClaim(claim, $event)"
                        :class="_.findIndex(toggled, {id : claim.id}) > -1 ? ' text-green '  : ' text-black-lt text-muted '"
                        class="pointer m-0 m-b-0">
                        <i
                                :class="_.findIndex(toggled, {id : claim.id}) > -1 ? ' fa-check-square ' : ' fa-square-o '"
                                class=" fa "
                                aria-hidden="true"
                        ></i>
                    </h4>
                </div>
                <div class="list-left">
                    <div class="avatar-thumbnail-container">
                        <div class="avatar-thumbnail _56">
                            <img :src="claim.listable_cover_photo_location">
                        </div>
                    </div>
                </div>
                <div class="list-body clearfix" style="margin-left: 126px;">
                    <div class="pull-md-left">
                        <span class="_500">{{ claim.full_name }} <template v-if="claim.is_guest"><span class="_400 text-sm">(Guest)</span></template></span>
                        <span class="text-sm text-muted block">Address: {{ claim.shipping_address }}</span>
                        <span class="text-sm text-muted block">Email: <a class="text-primary">{{ claim.email }}</a></span>
                        <span class="text-sm text-muted block">Item: <a class="text-primary">{{ claim.name_alt }} {{ claim.listable_price }}</a></span>
                        <span class="text-sm text-muted block">Sale: <a class="text-primary">{{ claim.sale_name }}</a></span>
                        <span class="text-sm text-muted block">Date: <timeago :timestamp="claim.claim_created_at.date"></timeago></span>
                        <!--<span class="text-sm block">-->
                        <!--<multiselect-->
                        <!--:id="claim.id"-->
                        <!--v-model="value"-->
                        <!--@tag="addTag"-->
                        <!--:options="options"-->
                        <!--:taggable="true"-->
                        <!--:multiple="true"-->
                        <!--:close-on-select="false"-->
                        <!--:clear-on-select="false"-->
                        <!--:hide-selected="true"-->
                        <!--placeholder="Pick some"-->
                        <!--label="name"-->
                        <!--track-by="name"></multiselect>-->
                        <!--</span>-->
                    </div>
                    <div class="pull-md-right">
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" class="btn btn-xs white dropdown-toggle no-caret">
                                <i aria-hidden="true" class="hidden-sm-down fa fa-ellipsis-h"></i>
                                <span class="hidden-md-up">Options</span>
                            </a>
                            <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                                <template v-if="claim.is_guest">
                                    <a class="dropdown-item">Accept</a>
                                    <div class="divider"></div>
                                </template>
                                <a class="dropdown-item">Return</a>
                                <a class="dropdown-item">View</a>
                            </div>
                        </div>
                        <!--<button v-if="claim.is_guest" class="btn white btn-xs">Accept</button>-->
                        <!--<button class="btn white btn-xs">Return</button>-->
                    </div>
                </div>
            </div>
        </div>
        <infinite-loading :distance="40" :on-infinite="fetchInfinite" ref="listingFinite">
            <span slot="no-more"></span>
            <span slot="no-results">
                <onboard-card class="onboard-pendingclaims">
                    <template slot="title">No pending claims or completed sales</template>
                    <template slot="subtext">
                        When an item you have listed is claimed, it will be displayed on this page.
                        <br>
                        Decide which claims you wish to return, returning the item to your inventory.
                    </template>
                </onboard-card>
            </span>
            <span slot="spinner"><spinny class="text-center center-block m-t-1" :size="'' + 38"></spinny></span>
        </infinite-loading>
    </div>
</template>
<style src="../multiselect/vue-multiselect.min.css"></style>
<style>
    #claims_actions_wrapper {
        left: 50%;
        max-width: 360px;
        padding: 10px 15px;
        opacity: 1;
        position: fixed;
        top: 130px;
        z-index: 1020;
        margin-left: -160px;
        padding: 5px;
    }
    .claims_actions_mini {
        margin-left: -108px !important;
    }
    .actions-menu {
        zoom: 1;
        background: #fff;
        border: 1px solid #c1cbd4;
        border-radius: 6px;
        box-shadow: 0 1px 7px 0 rgba(0,0,0,.08);
        display: block;
        min-height: 46px;
        position: relative;
        padding: 5px;
    }
    .actions-menu a {
        width: 96px;
    }
</style>
<script>
    import OnboardCard from '../OnboardCard.vue';
    import InfiniteLoading from 'vue-infinite-loading';
    import Spinny from  '../Spinner.vue';
    import Multiselect from 'vue-multiselect';
    import Timeago from "./../Timestamp.vue";
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            }
        },
        data: function () {
            return {
                toggled: [],
                value: [],
                claims: [],
                pagination: {},
                actions: {
                    fetching: false,
                },
                options: [
                    { name: 'Invoiced' },
                    { name: 'Paid' },
                    { name: 'Free' },
                ]
            }
        },
        computed: {
            toggledHasPending(){
                if (this.toggled.length) {
                    return _.find(this.toggled, {accepted_claim: null});
                }

                return false;
            },
        },
        methods: {
            bulkAccept(){},
            bulkReturn(){},
            bulkTag(){},
            toggleClaim(claim, event){
                let index = _.findIndex(this.toggled, {id : claim.id});
                if (index > -1) {
                    this.toggled.splice(index, 1);
                } else {
                    this.toggled.push(claim);
                }
            },
            /**
             * This method is called by the infiniteLoader
             * Instead of calling fetch items directly, this allows us to set a few variables
             * that fetch items will check.
             */
            fetchInfinite(){
                let url = this.pagination.next_page_url;

                if (this.claims.length == 0 || ! this.claims.length) {
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
                if (this.actions.fetching) {
                    return;
                }

                this.actions.fetching = true;

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
                    this.claims = response.body.data;
                    $Bus.$emit('listing-filter:completed', this.claims);
                } else {
                    _.each(response.body.data, (a)=>{
                        this.claims.push(a);
                    });
                    this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                }

                this.makePagination(response.body.meta.pagination);
                this.filtering = false;
                $Bus.$emit('fetch:completed', this.claims);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(()=>{
                    if (this.claims.length >= this.pagination.total) {
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
            },
            addTag (newTag) {
                const tag = {
                    name: newTag,
                };
                this.options.push(tag);
                this.value.push(tag);
            },
        },
        components: {
            'timeago': Timeago,
            'spinny' : Spinny,
            'onboard-card' : OnboardCard,
            Multiselect
        }
    }
</script>