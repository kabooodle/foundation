@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')


    <div class="pull-left">
        <div class="btn-toolbar center-block text-center">
            <button class="btn white btn-sm ">Filter</button>

            <div class="btn-group dropdown ">
                <button :disabled="selectedItems.length == 0" class="btn white btn-sm dropdown-toggle"
                        data-toggle="dropdown">
                    <span class="dropdown-label">Bulk</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">Delete</a>
                </div>
            </div>

            <a :disabled="selectedItems.length == 0"
               data-toggle="modal" data-target="#m-md"
               data-backdrop="static" data-keyboard="false" href="#"
               class=" btn_add_selected text-white  btn btn-sm warning">Add Selected
                Items to Sale</a>

            <a :disabled="selectedItems.length == 0"
               data-toggle="modal" data-target="#m-md-fb"
               data-backdrop="static" data-keyboard="false" href="#"
               class=" text-white btn_add_selected btn btn-sm warning">Add Selected
                Items to Facebook</a>

        </div>
    </div>


    <div class="pull-right">
        <a href="{{ route('shop.inventory.index', [user()->username]) }}" class="btn btn-sm white">Manage</a>
        <a href="{{ route('shop.inventory.create', [user()->username]) }}" class="btn btn-sm white">Add Items</a>
    </div>

@endsection


@section('body-content')
    <script type="text/javascript" src="http://cdn.jsdelivr.net/vue.table/1.5.3/vue-table.min.js"></script>


    <style>
        tr.highlight_row td {
            background-color: #fefbf2;
        }

        .table-wrapper {
            position: relative;
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
            top: 50%;
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
            padding: 3px 20px 3px 5px
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

    </style>

    <div class="box pull-left ">
        <div class="box-body">
            <form>
                <label>Style</label>
                {{ Form::select('style_id[]', $inventoryTypes->first()->styles->pluck('name','id')->toArray(), null, ['data-toggle' => 'multiselect', 'multiple']) }}
                <label>Size</label>
                {{ Form::select('size_id[]', $inventoryTypes->first()->styles->pluck('name','id')->toArray(), null, ['data-toggle' => 'multiselect', 'multiple']) }}
                <button type="button" v-on:click="filterSubmit" class="btn primary">Search</button>
            </form>
        </div>
    </div>

    <div class="table-wrapper pull-right">
        <div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
        <vuetable
                :fields="columns"
                :item-actions="itemActions"
                :append-params="moreParams"
                :selected-to="selectedItems"
                per-page="100"
                row-class-callback="rowClassCB"
                table-wrapper=".table-wrapper"
                pagination-path=""
                api-url="http://app.kabooodle.dev/shop/jaketoolson/inventory"
                table-class=" table table-condensed table-as-list white "
        ></vuetable>
    </div>


    <script>
        Vue.component('checkbox', {
            template: ['<input type="checkbox" @click="checkboxClicked(rowData, $event)">'].join(''),
            props: {
                rowData: {
                    type: Object,
                    required: true
                }
            },
            methods: {
                checkboxClicked: function (data, event) {
                    this.$dispatch('checkbox:clicked', event, data);
                }
            }
        });

        new Vue({
            el: '#manage_inventory',
            data: {
                moreParams: [],
                selectedItems: [],
                columns: [
                    {
                        name: '__component:checkbox',
                        titleClass: 'text-center',
                        dataClass: 'text-center'
                    },
                    {
                        name: 'item',
                        callback: 'setPropItem'
                    },
                    {
                        name: 'style',
                        callback: 'setPropStyle'
                    },
                    {
                        name: 'size',
                        callback: 'setPropSize'
                    },
                    {
                        name: 'price',
                        callback: 'setPropPrice'
                    },
                    'qty',
                    {
                        name: 'pending',
                        callback: 'setPropPending'
                    },
                    '__actions'
                ],
                itemActions: [
                    {
                        name: 'action-dropdown',
                        label: '',
                        icon: 'fa fa-caret-down',
                        class: ' btn btn-xs white'
                    }
                ]
            },
            watch: {
                selectedItems: function (v, m) {
                    // Listen to the selected items array.
                }
            },
            methods: {
                resetMoreParams: function(){
                    this.$data.moreParams = [];
                },
                filterSubmit: function (e) {
                    e.preventDefault();

                    var scope = this;
                    var el = $(e.target);
                    var form = el.closest('form');

                    scope.resetMoreParams();
                    scope.moreParams.push(form.serialize());

                    setTimeout(function(){
                        scope.$broadcast('vuetable:refresh');
                    },0);
                },
                setPropPending: function (v) {
                    return v.length;
                },
                setPropItem: function (v) {
                    var firstImg = v[0];
                    return '<img style="width: 32px; height: 32px; border-radius: 2px;" src="' + firstImg.location + '">';
                },
                setPropSize: function (v) {
                    return v.name;
                },
                setPropStyle: function (v) {
                    return v.name;
                },
                setPropPrice: function (v) {
                    return '$' + v;
                },
                viewProfile: function (id) {
                    console.log('view profile with id:', id)
                },
                rowClassCB: function (data, index) {
                    return 'p-l-0 m-l-0';
                }
            },
            events: {
                'vuetable:row-clicked': function (data, event) {
                    if ($.inArray(event.target.type, ['checkbox', 'button']) == -1) {
                        var checkboxEl = $(event.target).closest('tr').find(':checkbox');
                        checkboxEl.trigger('click');
                    }
                },
                'vuetable:action': function (action, data) {
                    console.log('vuetable:action', action, data);
                    if (action == 'view-item') {
                        this.viewProfile(data.id)
                    }
                },
                'vuetable:load-error': function (response) {
                    console.log('Load Error: ', response);
                },
                'checkbox:clicked': function (event, item) {
                    var scope = this;
                    setTimeout(function () {
                        var elTr = $(event.target).closest('tr');
                        var elCheckbox = elTr.find(':checkbox');

                        if (elCheckbox.is(':checked')) {
                            elTr.addClass('highlight_row');
                            scope.selectedItems.push(item);
                        } else {
                            elTr.removeClass('highlight_row');
                            scope.selectedItems.$remove(item);
                        }
                        // this.$broadcast('vuetable:refresh');
                    }, 0);
                }
            }
        })
    </script>


    {{--<table class="table table-condensed table-as-list white">--}}
    {{--<thead>--}}
    {{--<tr>--}}
    {{--<th>--}}
    {{--<div style="padding-left: 12px !important;"><input id="select_all" type="checkbox" class=""></div>--}}
    {{--</th>--}}
    {{--<th class="text-muted">Item</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0">Style</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0">Size</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0">Price</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0">*Qty</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0">Pending</th>--}}
    {{--<th class="text-muted p-l-0 m-l-0"></th>--}}
    {{--</tr>--}}
    {{--</thead>--}}
    {{--<tbody>--}}
    {{--@foreach($data as $item)--}}
    {{--<tr data-id="{{ $item->id }}">--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle" class="">--}}
    {{--<div class="list-item p-r-0 ">--}}
    {{--<input type="checkbox" class="selected_items_checkbox" name="selected_items[]"--}}
    {{--value="{{ $item->id }}">--}}
    {{--</div>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important">--}}
    {{--<div class="list ">--}}
    {{--<div class="list-item p-l-0 p-r-0">--}}
    {{--<div class="list-left">--}}

    {{--<span class="" style="width: 32px; height: 32px; border-radius: 2px;">--}}
    {{--<img style="width: 32px; height: 32px; border-radius: 2px;" src="{{ $item->firstImage() ? $item->firstImage()->location : 'https://placekitten.com/g/32/32'}}">--}}
    {{--</span>--}}
    {{--</div>--}}
    {{--@if($item->tagged->count() > 0)--}}
    {{--<div class="list-body">--}}
    {{--<div class="text-sm hidden-sm hidden-xs hidden-xs-down">--}}

    {{--<span class="text-muted">Categories: </span>--}}
    {{--@foreach($item->tagged as $tag) <span--}}
    {{--class="label label-xs outline text-u-c">{!! $tag->tag_name !!}</span> @endforeach--}}

    {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<span class="h5 ">   <a href="{{ route('shop.inventory.show', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}"--}}
    {{--class="">{{ $item->style->name }}</a></span>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<span class="h5 ">{{ $item->styleSize->name }}</span>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<span class="h5 ">${{ $item->price_usd }}</span>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<span class="h5">{{ $item->getAvailableQuantity() }}</span>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<span class="h5 "><a href="#"<a href="#"><span class="text-warning">{{ $item->pendingClaims->count() }}</span></a></span>--}}
    {{--</td>--}}
    {{--<td style="padding-bottom: 0; padding-top: 0; padding-right: 0; vertical-align: middle !important"--}}
    {{--valign="middle">--}}
    {{--<div class="pull-right list-item p-l-0">--}}
    {{--<div class="dropdown">--}}
    {{--<button data-toggle="dropdown" class="btn white btn-xs dropdown-toggle" type="button" ></button>--}}
    {{--<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">--}}
    {{--<a class="dropdown-item" href="{{ route('shop.inventory.show', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}" >Preview</a>--}}
    {{--<a class="dropdown-item" href="{{ route('shop.inventory.edit', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}">Edit</a>--}}
    {{--<a class="dropdown-item" href="#">Delete</a>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--</div>--}}


    {{--</td>--}}
    {{--</tr>--}}
    {{--@if($item->flashsales->count() > 0)--}}
    {{--<tr data-id="{{ $item->id }}">--}}
    {{--<td class="b-t-0 b-" style="border-top: 0"></td>--}}
    {{--<td colspan="8" class="b-t-0" style="border-top: 0">--}}
    {{--<div class="flashsale_wrapper">--}}
    {{--<span class="text-muted">Flash sales:</span>--}}
    {{--@foreach($item->flashsales as $flashsale)--}}
    {{--<span class="label outline">{{ $flashsale->name }} <a data-toggle="tooltip"--}}
    {{--data-route="{{ apiRoute('inventory.associate.destroy', [user()->username, $flashsale->pivot->id]) }}"--}}
    {{--data-placement="top"--}}
    {{--title="Remove from sale"--}}
    {{--class="m-l btn_remove_fromflashsale"--}}
    {{--data-item-id="{{ $item->id }}"--}}
    {{--data-flashsale-id="{{ $flashsale->id }}"--}}
    {{--href="#"><i--}}
    {{--class="fa fa-times"></i></a></span>--}}
    {{--@endforeach--}}
    {{--</div>--}}
    {{--</td>--}}
    {{--</tr>--}}
    {{--@endif--}}

    {{--@if($item->facebooksales->count() > 0)--}}
    {{--<tr data-id="{{ $item->id }}">--}}
    {{--<td class="b-t-0 b-" style="border-top: 0"></td>--}}
    {{--<td colspan="8" class="b-t-0" style="border-top: 0">--}}
    {{--<div class="flashsale_wrapper">--}}
    {{--<span class="text-muted">Facebook Albums:</span>--}}
    {{--@foreach($item->facebooksales as $facebooksale)--}}
    {{--<span class="label outline">{{ $facebooksale->facebook_post_id }} <a data-toggle="tooltip"--}}
    {{--data-route="{{ apiRoute('inventory.associate.destroy', [user()->username, $facebooksale->id]) }}"--}}
    {{--data-placement="top"--}}
    {{--title="Remove from sale"--}}
    {{--class="m-l btn_remove_fromflashsale"--}}
    {{--data-item-id="{{ $item->id }}"--}}
    {{--data-flashsale-id="{{ $facebooksale->id }}"--}}
    {{--href="#"><i--}}
    {{--class="fa fa-times"></i></a></span>--}}
    {{--@endforeach--}}
    {{--</div>--}}
    {{--</td>--}}
    {{--</tr>--}}
    {{--@endif--}}

    {{--@endforeach--}}
    {{--</tbody>--}}
    {{--</table>--}}


    {{--<div class="center-block text-center">--}}
    {{--{{ $data->links() }}--}}
    {{--</div>--}}

    <div id="m-md" class="modal" data-backdrop="true" style="display: none;">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{ Form::open(['class' => 'form-save']) }}
                        <div class="modal-header">
                            <h5 class="modal-title">Select sales to assign item</h5>
                        </div>
                        <div class="modal-body">
                            {{--<div class="form-group">--}}
                            {{--<label class="md-check">--}}
                            {{--<input type="checkbox" class="has-value" name="flashsales[]"--}}
                            {{--value="{{ user()->username }}">--}}
                            {{--<i class="green"></i>--}}
                            {{--Your Store--}}
                            {{--</label>--}}
                            {{--</div>--}}

                            @foreach(user()->flashsalesAsSellerAndAdmins as $flashSale)
                                <div class="form-group">
                                    <label class="md-check">
                                        <input type="checkbox" class="has-value" name="flashsales[]"
                                               value="{{ $flashSale->id }}">
                                        <i class="green"></i>
                                        {!! $flashSale->name !!}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn primary p-x-md btn_save_selected" id="">Save</button>
                            <button type="button" class="m-l-1 btn white" data-dismiss="modal">Cancel</button>
                        </div>
                        {{ Form::close() }}
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

    </div>





    <div id="m-md-fb" class="modal" data-backdrop="true" style="display: none;">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{ Form::open(['class' => 'form-save']) }}
                        <div class="modal-header">
                            <h5 class="modal-title">Select sales to assign item</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Facebook Group</label>
                                {{ Form::select('fb_group', user()->present()->getFacebookGroupsForList(), [] , ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group">
                                <label>Select Album(s)</label>
                                {{ Form::select('fb_albums[]', user()->present()->getFacebookAlbumsByFroupForList(327095390958693), [] , ['class' => 'form-control', 'multiple']) }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn primary p-x-md btn_save_selected" id="">Save</button>
                            <button type="button" class="m-l-1 btn white" data-dismiss="modal">Cancel</button>
                        </div>
                        {{ Form::close() }}
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

    </div>



    @push('footer-scripts')
    <script>

        {{--$(function () {--}}
        {{--var $selectAllEl = $('#select_all');--}}
        {{--var $selectedItemsEl = $('.selected_items_checkbox');--}}
        {{--var $addSelectedBtnEl = $('.btn_add_selected');--}}
        {{--var $selectedSaveBtnEl = $('.btn_save_selected');--}}
        {{--var $formSaveEl = $('.form-save');--}}

        {{--var href = $addSelectedBtnEl.prop('href');--}}



        {{--$selectAllEl.change(function (e) {--}}
        {{--e.preventDefault();--}}
        {{--if ($selectAllEl.is(':checked')) {--}}
        {{--$selectedItemsEl.each(function (i, e) {--}}
        {{--if (!$(e).is(':checked')) {--}}
        {{--$(e).prop('checked', true).trigger('change');--}}
        {{--}--}}
        {{--});--}}
        {{--} else {--}}
        {{--$selectedItemsEl.each(function (i, e) {--}}
        {{--if ($(e).is(':checked')) {--}}
        {{--$(e).prop('checked', false).trigger('change');--}}
        {{--}--}}
        {{--});--}}
        {{--}--}}
        {{--});--}}

        {{--$selectedItemsEl.change(function (e) {--}}
        {{--if ($(this).is(':checked')) {--}}
        {{--$(this).closest('tr').parent().find('tr[data-id="' + $(this).val() + '"]').addClass("highlight_row");--}}
        {{--$formSaveEl.append('<input type="hidden" name="inventoryids[]" id="inventory_item_id_' + $(this).val() + '" value="' + $(this).val() + '">');--}}
        {{--} else {--}}
        {{--$(this).closest('tr').parent().find('tr[data-id="' + $(this).val() + '"]').removeClass("highlight_row");--}}
        {{--$formSaveEl.find('#inventory_item_id_' + $(this).val()).remove();--}}
        {{--}--}}
        {{--var $selectedItemsCheckedEls = $('.selected_items_checkbox:checked');--}}
        {{--var totalChecked = $selectedItemsCheckedEls.length;--}}
        {{--if (totalChecked > 0) {--}}
        {{--$addSelectedBtnEl.removeClass('disabled').prop('disabled', false);--}}
        {{--} else {--}}
        {{--$addSelectedBtnEl.addClass('disabled').prop('disabled', true);--}}
        {{--$selectAllEl.prop('checked', false).trigger('change');--}}
        {{--}--}}
        {{--});--}}

        {{--$selectedSaveBtnEl.click(function (e) {--}}
        {{--e.preventDefault();--}}

        {{--var formData = $formSaveEl.serialize();--}}
        {{--var that = $(this);--}}

        {{--that.parent().find('button').addClass('disabled').prop('disabled', true);--}}
        {{--that.html('Processing...');--}}

        {{--$.ajax({--}}
        {{--url: "{{ apiRoute('inventory.associate.store', [user()->username]) }}",--}}
        {{--data: formData,--}}
        {{--type: "POST",--}}
        {{--dataType: "json"--}}
        {{--})--}}
        {{--.done(function (json) {--}}
        {{--window.location.href = window.location.href;--}}
        {{--})--}}
        {{--.fail(function (xhr, status, errorThrown) {--}}
        {{--alert("Sorry, there was a problem! Please try again.");--}}
        {{--that.parent().find('button').removeClass('disabled').prop('disabled', false);--}}
        {{--that.html('Save');--}}
        {{--});--}}
        {{--});--}}

        {{--var x = $('.btn_remove_fromflashsale');--}}

        {{--x.click(function (e) {--}}
        {{--e.preventDefault();--}}
        {{--var that = $(this);--}}

        {{--that.tooltip('dispose');--}}
        {{--that.addClass('disabled').attr('disabled', true).unbind('click');--}}
        {{--that.html('<i class="fa fa-spinner fa-spin"></i>');--}}

        {{--noty({--}}
        {{--text: 'Are you sure? This will remove the item from the flash sale',--}}
        {{--layout: 'center',--}}
        {{--theme: 'relax',--}}
        {{--type: 'alert',--}}
        {{--modal: true,--}}
        {{--animation: {--}}
        {{--open: {height: 'toggle'},--}}
        {{--close: {height: 'toggle'},--}}
        {{--easing: 'linear',--}}
        {{--speed: 1--}}
        {{--},--}}
        {{--timeout: 9000,--}}
        {{--buttons: [--}}
        {{--{--}}
        {{--addClass: 'btn btn-sm primary b-primary', text: 'Ok', onClick: function ($noty) {--}}
        {{--$noty.close();--}}

        {{--$.ajax({--}}
        {{--url: that.attr('data-route'),--}}
        {{--type: "DELETE",--}}
        {{--dataType: "json"--}}
        {{--})--}}
        {{--.done(function (json) {--}}
        {{--var wrapper = that.closest('.flashsale_wrapper');--}}
        {{--that.closest('.label').remove();--}}
        {{--if (wrapper.find('.label').length == 0) {--}}
        {{--wrapper.closest('tr').remove();--}}
        {{--}--}}
        {{--})--}}
        {{--.fail(function (xhr, status, errorThrown) {--}}
        {{--alert("Sorry, there was a problem! Please try again.");--}}
        {{--that.parent().find('button').removeClass('disabled').prop('disabled', false);--}}
        {{--that.html('<i class="fa fa-times"></i>');--}}
        {{--that.bind('click', true);--}}
        {{--});--}}
        {{--}--}}
        {{--},--}}
        {{--{--}}
        {{--addClass: 'btn btn-link white btn-sm', text: 'Cancel', onClick: function ($noty) {--}}
        {{--$noty.close();--}}
        {{--that.parent().find('button').removeClass('disabled').prop('disabled', false);--}}
        {{--that.html('<i class="fa fa-times"></i>');--}}
        {{--that.bind('click', true);--}}
        {{--}--}}
        {{--}--}}
        {{--]--}}
        {{--});--}}


        {{--});--}}

        {{--});--}}
    </script>
    @endpush

@endsection