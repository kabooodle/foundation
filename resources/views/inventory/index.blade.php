@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="center-block text-center" v-if="inventory_items.length > 0 && ! actions.refreshing_data">
        <button
                class="btn white btn-sm "
                data-toggle="tooltip"
                title="Refresh Inventory"
                data-placement="left"
                v-on:click="getInventory"
                :disabled="actions.refreshing_data">
            <i class="fa fa-refresh"></i>
        </button>
        <div class="btn-group dropdown ">
            <button
                    :disabled="selected.items.length == 0 || actions.refreshing_data"
                    v-bind:class="{'disabled' : selected.items.length == 0 || actions.refreshing_data }"
                    class=" btn white btn-sm dropdown-toggle"
                    data-toggle="dropdown">
                <span class="dropdown-label">Bulk</span><span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item text-danger" href="">Delete</a>
            </div>
        </div>
        <button
                v-bind:class="{'disabled' : actions.refreshing_data || inventory_items.length == 0}"
                :disabled="actions.refreshing_data || inventory_items.length == 0"
                class="btn white btn-sm"
        @click="selectAllInventory">
        Select All
        </button>
        <button
            v-cloak
                class="btn white btn-sm"
                :disabled="selected.items.length == 0 || actions.refreshing_data"
                v-bind:class="{'disabled' : selected.items.length == 0 || actions.refreshing_data }"
        @click="resetInventory">
        Unselect All (@{{selected.items.length}})
        </button>
        <button
                v-on:click="openPostMenu"
                :disabled="actions.refreshing_data || inventory_items.length == 0"
                v-bind:class="{'disabled' : actions.refreshing_data || inventory_items.length == 0}"
                class="btn primary btn-sm "
                id="navbarSideButton">List inventory to sales
        </button>
    </div>
@endsection


@push('header-styles')
<style>
    /*@media only screen and (max-width: 959px) {*/
    /*.navbar-side {*/
    /*width: 100% !important;*/
    /*}*/
    /*}*/
    .img-thumb {
        position: relative;
    }
    .img-thumb img {
        border-radius: .25rem;
    }

    .img-thumb:hover img {
        opacity: .4;
    }

    .img-thumb .fa {
        display: none;
    }

    .img-thumb:hover .fa {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        margin-top: -6px;
        margin-left: -1px;
        color: #f77a99;
    }


    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link.active {
        background: #fff !important;
    }

    .nav-tabs .nav-link {
        background: #ccc !important;
        border-bottom: 0;
    }
    [v-cloak] { display: none; }
</style>

@endpush

@section('body-content')
    <form id="post_sales_form" action="{{ apiRoute('inventory.associate.store', [user()->username]) }}"
          methods="POST">
        <div class="navbar-side" id="navbarSide">
            <div class="navbar-side-inner p-a" data-scrollable="scrollable">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a data-toggle="tab" @click="listingTypeChanged('facebook')" class="nav-link active" href="#post_facebook">Facebook
                            {{--<small class="block text-sm text-center text-muted">--}}
                            {{--(@{{ get_selected_facebook_sales.length }} items assigned)--}}
                            {{--</small>--}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" @click="listingTypeChanged('flashsale')" class="nav-link" href="#post_flashsales">
                            Flash Sales
                            {{--<small class="block text-sm text-center text-muted">--}}
                            {{--(@{{ get_selected_flashsales_sales.length }} selected)--}}
                            {{--@{{ sums.selected_postables }}--}}
                            {{--</small>--}}
                        </a>
                    </li>

                </ul>
                <div class="box">
                    <div class="tab-content p-a">
                        <div class="tab-pane" id="post_flashsales">
                            <div v-if="postables.flashsales && postables.flashsales.length > 0"
                                 v-for="flashsale in postables.flashsales"
                            :key="flashsale.id"
                            >
                                <div class="radio">
                                    <label>
                                        <input
                                                name="flashsalesales_id"
                                                :value="flashsale.id"
                                                {{--v-bind:checked="( get_selected_flashsales_sales.indexOf(flashsale.id) > -1 ? 'checked' : false )"--}}
                                                type="radio"
                                                v-on:click="toggleFlashSale(flashsale.id, $event)"> @{{ flashsale.name }}
                                        <span class="text-xs text-muted block">Sale dates: @{{ this.moment(flashsale.starts_at).format('MM/DD/YYYY') }} - @{{ this.moment(flashsale.ends_at).format('MM/DD/YYYY') }}</span>
                                            <span v-if="flashsale.my_post_time" class="text-xs text-muted block">Your listing time: @{{ this.moment(flashsale.my_post_time).format('MM/DD/YYYY hh:mm a') }}</span>
                                    </label>
                                    <div v-if="flashsale.id == selected.postables.flashsale && selected.items && selected.items.length">
                                        <span class="block text-muted text-sm">(@{{  selected.items.length }} items assigned)</span>
                                        <span
                                        @click="removeItemFromFlashsale(item, flashsale, flashsale.id, $event)"
                                        class="img-thumb" v-for="item in selected.items"
                                        :key="item.id"
                                        style="cursor:pointer; width: 24px; height: 24px; margin: 0 3px 3px 0;">
                                        <img
                                                v-bind:src="item.cover_photo.location"
                                                class="img-responsive"
                                                style="width: 24px; height: 24px;">
                                        <i class="fa fa-times fa-2x"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane active" id="post_facebook">
                            <div class="form-group">
                                <div class="clearfix">
                                    <label class="control-label">Select Group</label>
                                    <span class="pull-right">
                                        <facebook-login
                                                refresh_endpoint="{{ apiRoute('social.refresh') }}"
                                        ></facebook-login>
                                    </span>
                                </div>
                                <select
                                        :disabled="actions.getting_postables === true"
                                    :class="actions.getting_postables ? 'disabled' : null"
                                    id="post_facebook_group_el"
                                @change="changeFacebookGroup"
                                name="facebook_group"
                                placeholder="Select a group"
                                class="form-control">
                                <option></option>
                                <option
                                        v-if="postables.facebookgroups && postables.facebookgroups.length > 0"
                                        v-for="facebook_group in postables.facebookgroups"
                                        :key="facebook_group.id"
                                        :value="facebook_group.id">@{{ facebook_group.name }}
                                    (@{{facebook_group.albums ? facebook_group.albums.length : 0}} albums)
                                </option>
                                </select>
                            </div>
                            <template v-if="selected.fb_group">
                                <div v-if="postables.facebookgroups[postables.facebookgroups.indexOf(selected.fb_group)].albums.length > 0">
                                    <label class="control-label">Select Album</label>
                                    <div class="radio"
                                         v-for="facebook_album in postables.facebookgroups[postables.facebookgroups.indexOf(selected.fb_group)].albums"
                                         :key="facebook_album.id"
                                    >
                                        <label >
                                            <input
                                            @click="selectFacebookAlbum(facebook_album,$event)"
                                            type="radio"
                                            class="facebook_album_radio"
                                            name="facebookalbums[selected.fb_group.id]"
                                            :value="facebook_album.id"> @{{ facebook_album.name }}
                                        </label>
                                        <div v-if="facebook_album.items && facebook_album.items.length">
                                            <span class="block text-muted text-sm">(@{{  facebook_album.items.length }} items assigned)</span>
                                            <span
                                            @click="removeFromAlbum(item, facebook_album, selected.fb_group, $event)"
                                            class="img-thumb"

                                            v-for="item in facebook_album.items" style="cursor:pointer; width: 24px; height: 24px; margin: 0 3px 3px 0;"
                                            :key="item.id"
                                            >
                                            <img
                                                    v-bind:src="item.cover_photo.location"
                                                    class="img-responsive"
                                                    style="width: 24px; height: 24px;">
                                            <i class="fa fa-times fa-2x"></i>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <small class="text-muted">Selected group has no albums, or, you are not permitted to post to them. Login to Facebook or refresh your Facebook groups, above.</small>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
            <div class="savesales clearfix">
                <div class="pull-right ">
                    <button :disabled="actions.posting_to_sales"
                            :class="actions.posting_to_sales == true ? 'disabled' : null"
                            v-if="selected.listingtype == 'flashsale'" type="button"
                            class="btn btn-lg primary" @click="postSelectedItemsToFlashsale">
                        Save <spinner v-show="actions.posting_to_sales" size="24"></spinner>
                    </button>
                    <button v-if="selected.listingtype == 'facebook'" type="button"
                            class="btn btn-lg primary" data-toggle="modal" data-target="#kbdl-mdl-listings">
                        Continue <spinner v-show="actions.posting_to_sales" size="24"></spinner>
                    </button>
                    {{--<button--}}
                            {{--:disabled="( actions.posting_to_sales )"--}}
                            {{--:class="{'disabled' : actions.posting_to_sales }"--}}
                    {{--@click="postSelectedItemsToSales"--}}
                    {{--type="button"--}}
                    {{--class="btn btn-lg primary"--}}
                    {{-->--}}
                    {{--List Items <spinner v-show="actions.posting_to_sales" size="24"></spinner>--}}
                    {{--</button>--}}
                    <button
                            :disabled="( actions.posting_to_sales )"
                            :class="{'disabled' : actions.posting_to_sales }"
                            type="button"
                            class="btn btn-lg white"
                    @click="closePostMenu"
                    >
                    Close
                    </button>
                </div>
            </div>
        </div>
    </form>

    <style-template></style-template>
    <listing-settings></listing-settings>

    <onboard-card class="onboard-manageinventory" v-if="inventory_items.length == 0 && ! actions.refreshing_data">
        <template slot="title">No inventory to manage or list</template>
        <template slot="subtext">
            Once you've added inventory, you can list it to Facebook &amp; flash sales anytime!
            <br>
            Wish to edit an item? You would do that here too :)
        </template>
        <template slot="extra"><button class="btn btn-lg btn-grn m-b-2"><a href="{{ route('shop.inventory.create', [user()->username]) }}" >Got it! Take me to add inventory</a></button></template>
    </onboard-card>

@endsection

@push('footer-scripts')
<script>
    const inventory_route = '{{ apiRoute('inventory.index', [user()->username]) }}';
</script>
<script src="{{ staticAsset("/assets/js/inventory-management.js") }}"></script>
@endpush