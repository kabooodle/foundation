@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="center-block text-center">
        <button
                class="btn white btn-sm "
                data-toggle="tooltip"
                title="Refresh Inventory"
                data-placement="top"
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

    .reveal {
        right: 0 !important;;
    }

    .navbar-side {
        -webkit-transform: translateX(0%);
        -ms-transform: translateX(0%);
        transform: translateX(0%);
        -webkit-transition: 300ms ease;
        transition: 300ms ease;
        height: 100% !important;
        width: 450px;
        position: fixed;
        top: 64px;
        bottom: 0;
        right: -460px;
        padding: 0;
        list-style: none;
        background-color: rgba(43, 41, 56, .95);
        z-index: 2000;
    }

    .navbar-side-inner {
        /*height: 100%;*/
        /*width: 100%;*/
        /*margin-bottom: 60px;*/
        /*overflow-y: scroll;*/
    }

    .savesales {
        position: fixed;
        bottom: 100px;
        left: 0;
        padding: 10px;
        margin: 0;
        width: 100%;
    }

    tr.highlight_row td {
        background-color: #fefbf2;
    }

    .table-wrapper {
        position: relative;
    }

    .table-wrapper tbody tr:hover {
        cursor: pointer;
    }

    /* Loading Animation: */
    .vuetable-wrapper {
        position: relative;
        opacity: 1;
    }

    .loader {
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s linear;
        width: 200px;
        height: 30px;
        font-size: 1em;
        text-align: center;
        margin-left: -100px;
        letter-spacing: 4px;
        color: #f77a99;
        position: absolute;
        top: 20px;
        left: 50%;
    }

    .loading .loader {
        visibility: visible;
        opacity: 1;
        z-index: 100;
    }

    .loading .vuetable {
        opacity: 0.3;
        filter: alpha(opacity=30);
    }

    span.multiselect-native-select {
        position: relative
    }

    span.multiselect-native-select select {
        border: 0 !important;
        clip: rect(0 0 0 0) !important;
        height: 1px !important;
        margin: -1px -1px -1px -3px !important;
        overflow: hidden !important;
        padding: 0 !important;
        position: absolute !important;
        width: 1px !important;
        left: 50%;
        top: 30px
    }

    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }

    .multiselect-container .input-group {
        margin: 5px
    }

    .multiselect-container > li {
        padding: 0
    }

    .multiselect-container > li > a.multiselect-all label {
        font-weight: 700
    }

    .multiselect-container > li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }

    .multiselect-container > li.multiselect-group-clickable label {
        cursor: pointer
    }

    .multiselect-container > li > a {
        padding: 0
    }

    .multiselect-container > li > a > label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 20px 3px 10px
    }

    .multiselect-container > li > a > label.radio,
    .multiselect-container > li > a > label.checkbox {
        margin: 0
    }

    .multiselect-container > li > a > label > input[type=checkbox] {
        margin-bottom: 5px
    }

    .btn-group > .btn-group:nth-child(2) > .multiselect.btn {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px
    }

    .form-inline .multiselect-container label.checkbox,
    .form-inline .multiselect-container label.radio {
        padding: 3px 20px 3px 40px
    }

    .form-inline .multiselect-container li a label.checkbox input[type=checkbox],
    .form-inline .multiselect-container li a label.radio input[type=radio] {
        margin-left: -20px;
        margin-right: 0
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
        <form id="post_sales_form" action="{{ apiRoute('inventory.associate.store', [user()->username]) }}"
              methods="POST">
            <div class="navbar-side-inner p-a">
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
        </form>
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