@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="center-block text-center">
        <button
                class="btn white btn-sm "
                data-toggle="tooltip"
                title="Refresh Inventory"
                data-placement="top"
                v-on:click="getInventory"
                :disabled="refreshing_data">
            <i class="fa fa-refresh"></i>
        </button>
        <div class="btn-group dropdown ">
            <button
                    :disabled="selected_items.length == 0 || refreshing_data"
                    v-bind:class="{'disabled' : selected_items.length == 0 || refreshing_data }"
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
                :disabled="selected_items.length == 0 || refreshing_data"
                v-bind:class="{'disabled' : selected_items.length == 0 || refreshing_data }"
        @click="resetInventory">
        Unselect All (@{{selected_items.length}})
        </button>
        <button
                v-on:click="openPostMenu"
                :disabled="refreshing_data"
                v-bind:class="{'disabled' : refreshing_data }"
                class="btn primary btn-sm "
                id="navbarSideButton">Post To Sales</button>
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
        background-color: rgba(43,41,56, .95);
        z-index: 2000;
    }
    .navbar-side-inner {
        height: 100%;
        width: 100%;
        margin-bottom: 60px;
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
        <form id="post_sales_form" action="{{ apiRoute('inventory.associate.store', [user()->username]) }}" methods="POST">
        <div class="navbar-side-inner p-a">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a data-toggle="tab" class="nav-link active" href="#post_flashsales">
                        Flash Sales
                        <small class="block text-sm text-center text-muted">(@{{ get_selected_flashsales_sales.length }} Selected)</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a data-toggle="tab" class="nav-link" href="#post_facebook">Facebook
                        <small class="block text-sm text-center text-muted">(@{{ get_selected_facebook_sales.length }} Selected)</small>
                    </a>
                </li>
            </ul>
            <div class="box">
                <div class="tab-content p-a">
                    <div class="tab-pane active" id="post_flashsales">
                        @foreach(user()->flashsalesAsSellerAndAdmins as $flashSale)
                            <div class="checkbox">
                                <label>
                                    <input
                                            name="flashsalesales_ids[]"
                                            value="{{ $flashSale->id  }}"
                                            v-bind:checked="( get_selected_flashsales_sales.indexOf({{ $flashSale->id }}) > -1 ? 'checked' : false )"
                                            type="checkbox"
                                            v-on:change="toggleFlashSale({{ $flashSale->id }}, $event)"> {{ $flashSale->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="tab-pane" id="post_facebook">
                        <div class="form-group">
                            <label>Post on a specific date and time</label>
                            <div class="input-group">
                                {{ Form::text('ends_at', null, ['class' => 'form-control', 'id' => 'datetimepicker2', 'v-bind' => 'date_time_input']) }}
                                <span class="input-group-btn">
                                    <button
                                            @click="clearDateTimeInput"
                                            class="btn white"
                                            type="button"
                                            style="padding-bottom:.3rem;">Clear</button>
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
                            <div class="radio" v-for="facebook in facebooks">
                                <label>
                                    <input
                                            @click="selectFacebookAlbum(facebook,$event)"
                                            type="radio"
                                            name="facebookalbums"
                                            :value="facebook.id"> Album - @{{ facebook.name }}
                                </label>
                                <div v-if="facebook.fitems.length > 0">
                                    <span class="block text-muted text-sm">(@{{  facebook.fitems.length }} items assigned)</span>
                                    <span v-for="fitem in facebook.fitems" style="width: 24px; height: 24px; margin: 0 3px 3px 0;">
                                         <img
                                        @click="removeFromAlbum(fitem, facebook, $event)"
                                                 v-bind:src="(fitem.files && fitem.files.length > 0 ? fitem.files[0].location : 'http://lorempizza.com/24/24/'+fitem.id)"
                                                 class="img-responsive"
                                                    :data-id="fitem.id"
                                                 style="width: 24px; height: 24px;">
                                    </span>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="savesales clearfix">
            <div class="pull-right ">
                <button
                @click="postSelectedItemsToSales"
                type="button"
                :disabled="(!has_selected_sales_and_selected_items || posting_to_sales )"
                v-bind:class="{'disabled' : !has_selected_sales_and_selected_items || posting_to_sales }"
                class="btn btn-lg primary"
                >Post @{{ selected_sales_sum }} Items</button>
                <button
                        :disabled="(posting_to_sales )"
                        v-bind:class="{'disabled' :  posting_to_sales }"
                        v-on:click="closePostMenu"
                        type="button"
                        class="btn btn-lg white"
                >Close</button>
            </div>
        </div>
        </form>
    </div>


    <popout-overlay></popout-overlay>

    <style-template
            :selected_items.sync="selected_items"
            :data_items.sync="data_items"
            :refreshing_data.sync="refreshing_data">
    </style-template>

@endsection

@push('footer-scripts')
<script src="/assets/js/inventory-management.js"></script>
<script>
    $(function(){
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