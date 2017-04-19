<template>
    <div>
        <modal
                modal_id="claims_modal"
        >
            <div slot="modal_body"></div>
        </modal>
        <div class="inventory-overlay" v-if="actions.bulk_processing"></div>
        <template v-if="toggled.length">
            <div id="claims_actions_wrapper"
                 :class="toggledHasPending || toggledHasShipped ? null : ' claims_actions_mini '"
            >
                <div class="actions-menu">
                    <a
                            v-if="toggledHasPending"
                            class="btn btn-sm white"
                            @click="bulkAccept"
                            href="#assignMenu"
                            :class="actions.bulk_processing ? ' disabled ' : null"
                            :disabled="actions.bulk_processing"
                    >
                        Accept
                    </a>
                    <a
                            class="btn btn-sm white"
                            href="#assignMenu"
                            @click="bulkReturn"
                            :class="actions.bulk_processing ? ' disabled ' : null"
                            :disabled="actions.bulk_processing"
                    >
                        Return
                    </a>
                    <a
                            v-if="toggledHasShipped"
                            class="btn btn-sm white"
                            href="#assignMenu"
                            @click="bulkShippingLabel"
                            :class="actions.bulk_processing ? ' disabled ' : null"
                            :disabled="actions.bulk_processing"
                    >
                        Get Labels
                    </a>
                    <a
                            v-if="!actions.tagging"
                            class="btn btn-sm white"
                            href="#assignMenu"
                            @click="toggleTagging"
                            :class="actions.bulk_processing ? ' disabled ' : null"
                            :disabled="actions.bulk_processing"
                    >
                        Tag
                    </a>
                    <a
                            v-if="actions.tagging && tags"
                            class="btn btn-sm white"
                            href="#assignMenu"
                            @click="bulkTag"
                            :class="actions.bulk_processing || tag.length == 0 ? ' disabled ' : null"
                            :disabled="actions.bulk_processing || tag.length == 0"
                    >
                        Save
                    </a>
                    <div class="block text-center text-xs m-t-xs">
                        <template v-if="actions.tagging">
                            <multiselect
                                    v-model="tag"
                                    :options="tags"
                                    tag-placeholder="Add this tag"
                                    placeholder="Select or create a tag"
                                    label="name"
                                    track-by="name"
                                    :multiple="true"
                                    :taggable="true"
                                    @tag="addTag"
                            ></multiselect>
                        </template>
                        <span class="text-muted ">{{ toggled.length }} Claims Selected</span>
                        <spinny v-if="actions.bulk_processing"></spinny>
                    </div>
                </div>
            </div>
        </template>
        <div class="list box-shadow-z0 white">
            <div
                    v-for="claim in claims"
                    :key="claim.id"
                    :data-claim-id="claim.uuid"
                    :class="claim.accepted_claim == 1 ?  null : ' b-l b-l-2x b-l-primary list-item-unread '"
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
                        <div class="avatar-thumbnail _82">
                            <img :src="claim.listable_cover_photo_location">
                        </div>
                    </div>
                </div>
                <div class="list-body clearfix" style="margin-left: 126px;">
                    <div class="pull-md-left">
                        <span class="_500">{{ claim.full_name }} <template v-if="claim.is_guest"><span
                                class="_400 text-sm">(Guest)</span></template></span>
                        <span class="container_address text-sm text-muted block">Address: {{ claim.shipping_address }}</span>
                        <span class="container_email text-sm text-muted block">Email: <a :href="'mailto:'+claim.email"
                                class="text-u-l-on-hover text-primary">{{ claim.email }}</a>
                        <a v-if="!claim.is_guest" class="text-primary text-u-l-on-hover" :href="claim.profile_endpoint">(View Profile)</a>
                        </span>
                        <span class="container_item text-sm text-muted block">Item: <a :href="claim.listing_item_endpoint" class="text-u-l-on-hover text-primary">{{ claim.name_alt }} {{ claim.listable_price }}</a></span>
                        <span class="container_sale text-sm text-muted block">Sale:
                            <a :href="claim.listing_endpoint" v-if="claim.sale_name" class="text-u-l-on-hover text-primary">{{ claim.sale_name }}</a>
                            <template v-else><i>Claimed manually</i></template>
                        </span>
                        <span class="text-sm text-muted block">Date: <timeago
                                :timestamp="claim.claim_created_at.date"></timeago>, {{ claim.claim_created_at.date }}
                        </span>
                        <span v-if="claim.tag_name" class="text-sm text-muted block">Tags: <span
                                v-html="displayTags(claim.tag_name)"></span></span>
                        <template v-if="claim.is_merchant_plus && claim.accepted_claim == 1">
                            <span class="text-sm text-muted block">Shipping:
                                <span v-if="claim.shipped_via_kabooodle" v-html="claim.shipping_status"></span>
                                <a v-else :href="claim.shipping_create_endpoint" class="text-primary text-u-l-on-hover">Create shipping label</a>
                            </span>
                        </template>
                    </div>
                    <div class="pull-md-right">
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" class="btn btn-xs white dropdown-toggle no-caret">
                                <i aria-hidden="true" class="hidden-sm-down fa fa-ellipsis-h"></i>
                                <span class="hidden-md-up">Options</span>
                            </a>
                            <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                                <template v-if="claim.accepted_claim == 0">
                                    <a @click="acceptClaim(claim, $event)" class="dropdown-item">Accept</a>
                                    <div class="divider"></div>
                                </template>
                                <a :href="claim.shipping_transaction_endpoint" class="dropdown-item">View Claim</a>
                                <template v-if="claim.is_merchant_plus">
                                    <div class="divider"></div>
                                    <template v-if="claim.shipped_via_kabooodle">
                                        <a  :href="claim.shipping_transaction_endpoint" class="dropdown-item">View shipment info</a>
                                        <a  :href="claim.shipping_label_endpoint" class="dropdown-item">View shipment label</a>
                                    </template>
                                    <a v-else :href="claim.shipping_create_endpoint" class="dropdown-item">Create shipping label</a>
                                </template>
                                <div class="divider"></div>
                                <a @click="returnClaim(claim, $event)" class="dropdown-item">Return Item</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <infinite-loading :on-infinite="fetchInfinite" ref="listingFinite">
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
        width: 350px;
        padding: 10px 15px;
        position: fixed;
        top: 130px;
        z-index: 9998;
        margin-left: -170px;
        padding: 5px;
    }
    .claims_actions_mini {
        width: 240px !important;
        margin-left: -120px !important;
    }
    .actions-menu {
        zoom: 1;
        background: #fff;
        border: 1px solid #c1cbd4;
        border-radius: 6px;
        box-shadow: 0 1px 7px 0 rgba(0, 0, 0, .08);
        display: block;
        min-height: 46px;
        position: relative;
        padding: 5px;
    }
    .actions-menu a {
        width: 106px;
    }
</style>
<script>
    import Modal from '../Modal.vue';
    import OnboardCard from '../OnboardCard.vue';
    import InfiniteLoading from 'vue-infinite-loading';
    import Spinny from  '../Spinner.vue';
    import Timeago from "./../Timestamp.vue";
    import Multiselect from 'vue-multiselect';
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            },
            accept_endpoint: {
                required: true,
                type: String
            },
            return_endpoint: {
                required: true,
                type: String
            },
            label_endpoint: {
                required: true,
                type: String
            },
        },
        data: function () {
            return {
                toggled: [],
                tag: [],
                tags: [
                    {name: 'invoiced'},
                    {name: 'paid'},
                ],
                claims: [],
                pagination: {},
                actions: {
                    bulk_processing: false,
                    fetching: false,
                    tagging: false,
                },
            }
        },
        watch: {
            toggled(v,o){
                if (! v || v.length == 0) {
                    this.actions.tagging = false;
                    this.tag = [];
                }
            }
        },
        computed: {
            toggledHasPending(){
                if (this.toggled.length) {
                    return _.find(this.toggled, {accepted_claim: 0});
                }

                return false;
            },
            toggledHasShipped(){
                if (this.toggled.length) {
                    return _.reject(this.toggled, {'shipping_transaction_uuid' : null}).length > 0;
                }

                return false;
            },
        },
        methods: {
            displayTags(tags){
                let html = '';
                _.each(tags.split(','), (tag) => {
                    html += '<span class="label blue-grey m-r-xs">' + tag + '</span>';
                })

                return html;
            },

            acceptClaim(claim){
                this.actions.bulk_processing = true;
                this.$http.post(this.accept_endpoint, {claims: [claim.id]}).then((response) => {
                    this._handleResponseClaims(response.body.data);
                }).finally(() => {
                    this.actions.bulk_processing = false;
                })
            },

            returnClaim(claim){
                confirmModal(($noty) => {
                    this._disableNotyButtons($noty);
                    this.$http.post(this.return_endpoint, {claims: [claim.id]}).then((response) => {
                        let index = _.findIndex(this.claims, {id: claim.id});
                        if (index > -1) {
                            this.claims.splice(index, 1);
                        }
                        this._triggerNotice();
                    }).finally(() => {
                        this.actions.bulk_processing = false;
                        $noty.close();
                    })
                });
            },

            bulkAccept(){
                this.actions.bulk_processing = true;
                this.$http.post(this.accept_endpoint, {claims: _.map(this.toggled, 'id')}).then((response) => {
                    this._handleResponseClaims(response.body.data);
                    this._triggerNotice();
                }).finally(() => {
                    this.actions.bulk_processing = false;
                    this.toggled = [];
                })
            },

            bulkReturn(){
                confirmModal(($noty) => {
                    this._disableNotyButtons($noty);
                    this.actions.bulk_processing = true;
                    this.$http.post(this.return_endpoint, {claims: _.map(this.toggled, 'id')}).then((response) => {
                        _.each(this.toggled, (claim) => {
                            let index = _.findIndex(this.claims, {id: claim.id});
                            if (index > -1) {
                                this.claims.splice(index, 1);
                            }
                        });
                        this._triggerNotice();
                    }).finally(() => {
                        this.actions.bulk_processing = false;
                        this.toggled = [];
                        $noty.close();
                    })
                });
            },

            toggleTagging(){
                this.actions.tagging = !! this.actions.tagging == false;
            },

            bulkShippingLabel(){},

            bulkTag(){
                this.actions.bulk_processing = true;
                this.$http.post(this.label_endpoint, {
                    labels: _.map(this.tag, 'name'),
                    claims: _.map(this.toggled, 'id')
                }).then((response) => {
                    this._handleResponseClaims(response.body.data);
                    this._triggerNotice();
                }).finally(() => {
                    this.actions.bulk_processing = false;
                    this.actions.tagging = false;
                    this.toggled = [];
                    this.tag = [];
                })
            },

            _disableNotyButtons($noty){
                $noty.$buttons.find('.noty-btn-primary').html(spinny()).end().find('.noty-btn').addClass('disabled').prop('disabled', true);
            },

            _triggerNotice(type, msg){
                notify({
                    type: type ? type : 'success',
                    text: msg ? msg : 'Success'
                });
            },

            _handleResponseClaims(claims){
                _.each(claims, (claim) => {
                    let index = _.findIndex(this.claims, {id: claim.id});
                    if (index > -1) {
                        this.claims[index] = claim;
                    }
                });
            },

            toggleClaim(claim, event){
                let index = _.findIndex(this.toggled, {id: claim.id});
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

                if (this.claims.length == 0 || !this.claims.length) {
                    url = this.fetch_endpoint;
                }


                // Check if there is a next_page_url as per our pagination.
                // If there isn't, that means we've reached the end, so we dont need to continue fetching.
                if (url) {
                    this.fetchItems(url);
                } else {
                    this.$nextTick(() => {
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

                this.$http.get(url, {params: data}).then((response) => {
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
                    if (response.body.data.length == 0) {
                        this.$refs.listingFinite.$emit('$InfiniteLoading:complete');
                        this.actions.fetching = false;
                        return;
                    }

                    _.each(response.body.data, (a) => {
                        this.claims.push(a);
                    });
                    this.$refs.listingFinite.$emit('$InfiniteLoading:loaded');
                }

                this.makePagination(response.body.meta.pagination);
                this.filtering = false;
                $Bus.$emit('fetch:completed', this.claims);

                // If we have reached the end, tell our infinite loader we're completed,
                // otherwise, tell it we're loaded and ready for next...
                this.$nextTick(() => {
                    if (this.claims.length >= this.pagination.total || this.pagination.total == 0) {
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
            makePagination: function (data) {
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
                this.tags.push(tag);
                this.tag.push(tag);
            },
        },
        components: {
            'timeago': Timeago,
            'spinny': Spinny,
            'onboard-card': OnboardCard,
            'modal' : Modal,
            Multiselect
        }
    }
</script>