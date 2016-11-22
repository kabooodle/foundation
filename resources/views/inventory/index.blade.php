@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')


    <div class="center-block text-center">
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
                class="btn white btn-sm"
        @click="selectAllInventory">
        Select All
        </button>
        <button
                class="btn white btn-sm"
                :disabled="selected.items.length == 0 || actions.refreshing_data"
                v-bind:class="{'disabled' : selected.items.length == 0 || actions.refreshing_data }"
        @click="resetInventory">
        Unselect All (@{{selected.items.length}})
        </button>
        <button
                v-on:click="openPostMenu"
                :disabled="actions.refreshing_data"
                v-bind:class="{'disabled' : actions.refreshing_data }"
                class="btn primary btn-sm "
                id="navbarSideButton">Post To Sales
        </button>
        {{--<button class="btn white btn-sm " id="navbarSideButton">Filter Items</button>--}}
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
</style>

@endpush

@section('body-content')

    <div
            class="navbar-side"
            id="navbarSide">
            <div class="navbar-side-inner p-a">
                <form id="post_sales_form" action="{{ apiRoute('inventory.associate.store', [user()->username]) }}"
                      methods="POST">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link active" href="#post_flashsales">
                            Flash Sales
                            {{--<small class="block text-sm text-center text-muted">--}}
                                {{--(@{{ get_selected_flashsales_sales.length }} selected)--}}
                                {{--@{{ sums.selected_postables }}--}}
                            {{--</small>--}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link" href="#post_facebook">Facebook
                            {{--<small class="block text-sm text-center text-muted">--}}
                                {{--(@{{ get_selected_facebook_sales.length }} items assigned)--}}
                            {{--</small>--}}
                        </a>
                    </li>
                </ul>
                <div class="box">
                    <div class="tab-content p-a">
                        <div class="tab-pane active" id="post_flashsales">
                            <div v-if="postables.flashsales && postables.flashsales.length > 0"
                                 v-for="flashsale in postables.flashsales">
                                <div class="checkbox">
                                    <label>
                                        <input
                                                name="flashsalesales_ids[]"
                                                :value="flashsale.id"
                                                {{--v-bind:checked="( get_selected_flashsales_sales.indexOf(flashsale.id) > -1 ? 'checked' : false )"--}}
                                                type="checkbox"
                                                v-on:change="toggleFlashSale(flashsale.id, $event)"> @{{ flashsale.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="post_facebook">
                            <div class="form-group">
                                <label>Select Group</label>
                                <select
                                @change="changeFacebookGroup"
                                name="facebook_group"
                                placeholder="Select a group"
                                class="form-control">
                                <option></option>
                                <option
                                        v-if="postables.facebookgroups && postables.facebookgroups.length > 0"
                                        v-for="facebook_group in postables.facebookgroups"
                                        :value="facebook_group.id">@{{ facebook_group.name }}
                                    (@{{facebook_group.albums ? facebook_group.albums.length : 0}} albums)
                                </option>
                                </select>
                            </div>
                            <template v-if="selected.fb_group">
                                <template v-if="actions.fb_advanced_menu">
                                    <div class="form-group">
                                        <label>Post on a specific date and time</label>
                                        <div class="input-group">
                                            {{ Form::text('ends_at', null, ['class' => 'form-control', 'id' => 'datetimepicker2', 'v-bind' => 'date_time_input']) }}
                                            <span class="input-group-btn">
                                    <button
                                                @click="clearDateTimeInput"
                                            class="btn white"
                                            type="button"
                                            style="padding-bottom: .3rem
                                                ;">Clear</button>
                                </span>
                                        </div>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input
                                                    name="include_description_text"
                                                    value="1"
                                                    type="checkbox"> Include Link in description
                                        </label>
                                    </div>
                                </template>
                                <template v-if="postables.facebookgroups[postables.facebookgroups.indexOf(selected.fb_group)].albums.length > 0">
                                    <label>Select Album</label>
                                    <div class="radio"
                                         v-for="facebook_album in postables.facebookgroups[postables.facebookgroups.indexOf(selected.fb_group)].albums">
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
                                            class="img-thumb" v-for="item in facebook_album.items" style="cursor:pointer; width: 24px; height: 24px; margin: 0 3px 3px 0;">
                                                <img
                                                v-bind:src="(item.files && item.files.length > 0 ? item.files[0].location : 'http://lorempizza.com/64/64/'+item.id)"
                                                class="img-responsive"
                                                style="width: 24px; height: 24px;">
                                                <i class="fa fa-times fa-2x"></i>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <small class="text-muted">Selected group has no albums, or, you are not permitted to post to them.</small>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            {{--<div class="savesales clearfix">--}}
            {{--<div class="pull-right ">--}}
            {{--<button--}}
            {{--@click="postSelectedItemsToSales"--}}
            {{--type="button"--}}
            {{--:disabled="(!has_selected_sales_and_selected.items || posting_to_sales )"--}}
            {{--v-bind:class="{'disabled' : !has_selected_sales_and_selected.items || posting_to_sales }"--}}
            {{--class="btn btn-lg primary"--}}
            {{-->Post @{{ selected_sales_sum }} Items</button>--}}
            {{--<button--}}
            {{--:disabled="(posting_to_sales )"--}}
            {{--v-bind:class="{'disabled' :  posting_to_sales }"--}}
            {{--v-on:click="closePostMenu"--}}
            {{--type="button"--}}
            {{--class="btn btn-lg white"--}}
            {{-->Close</button>--}}
            {{--</div>--}}
            {{--</div>--}}

    </div>


    <style-template></style-template>

@endsection

@push('footer-scripts')
<script>
    const inventory_route = '{{ apiRoute('inventory.index', [user()->username]) }}';
</script>
<script src="/assets/js/inventory-management.js"></script>
<script>
    $(function () {

        $('#datetimepicker2').datetimepicker({
            format: "MM/DD/YYYY hh:mmA",
            minDate: new Date(),
            sideBySide: true,
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            }
        });
    })
</script>
@endpush