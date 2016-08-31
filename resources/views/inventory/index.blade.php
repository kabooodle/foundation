@extends('layouts.body_w_leftnav')

@section('body-menu')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"/>
    @endpush

    <div class="pull-left">
        <div class="btn-toolbar center-block text-center">
            <div class="btn-group dropdown">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Filter</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">By Name</a>
                    <a class="dropdown-item" href="">By Quantity</a>
                </div>
            </div>

            <div class="btn-group dropdown ">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Bulk</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">Delete</a>
                </div>
            </div>

            <a style="display: none" id="btn_add_selected" data-toggle="modal" data-target="#m-md"
               data-backdrop="static" data-keyboard="false" href="#" class="text-white  btn btn-sm warning">Add Selected
                Items to Sale</a>

        </div>
    </div>


    <div class="pull-right">
        <a href="{{ route('shop.inventory.index', [user()->username]) }}" class="btn btn-sm white">Manage</a>
        <a href="{{ route('shop.inventory.create', [user()->username]) }}" class="btn btn-sm white">Add Items</a>
    </div>

@endsection


@section('body-content')

    <style>
        tr.highlight_row td {
            background-color: #fefbf2;
        }
    </style>

    <table class="table table-condensed table-as-list white">
        <thead>
        <tr>
            <th>
                <div style="padding-left: 12px !important;"><input id="select_all" type="checkbox" class=""></div>
            </th>
            <th class="text-muted">Details</th>
            <th class="text-muted">*In-stock</th>
            <th class="text-muted">Price</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle" class="">
                    <div class="list-item p-r-0 @if($item->flashsales->count() > 0) b-success b-l b-l-2x @endif ">
                        <input type="checkbox" class="selected_items_checkbox" name="selected_items[]"
                               value="{{ $item->id }}">
                    </div>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important">
                    <div class="list ">
                        <div class="list-item p-l-0 p-r-0">
                            <div class="list-left">

                        <span class="w-40 avatar">
                                            <img src="https://placekitten.com/g/32/32">
                                          </span>
                            </div>
                            <div class="list-body">
                                <div>
                                    <a href="{{ route('shop.inventory.show', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}"
                                       class="_500 h6">{{ $item->name }}</a>
                                </div>
                                <div class="text-ellipsis text-muted text-sm">
                                    Categories: @foreach($item->categories as $cat) {{ $cat->name }} @endforeach</div>
                                <div class="text-sm hidden-sm hidden-xs hidden-xs-down">
                                    @if($item->tagged->count() > 0)
                                        <span class="text-muted">Tags: </span>
                                        @foreach($item->tagged as $tag) <span
                                                class="label label-xs text-u-c">{!! $tag->tag_name !!}</span> @endforeach
                                    @endif
                                    {{--<div class="text-sm text-muted">--}}
                                    {{--Last Updated: {{ $item->updated_at }}, Added On: {{ $item->created_at }}--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle">
                    <span class="h5">{{ $item->current_qty }}</span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle">
                    <span class="h5 ">${{ $item->price_usd }}</span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle">
                    <div class="text-right text-muted p-r-1">
                        <a href="{{ route('shop.inventory.show', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}"
                           class="btn white btn-sm _400">View</a> <a
                                href="{{ route('shop.inventory.edit', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}"
                                class="btn white btn-sm _400">Edit</a>
                    </div>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <div class="center-block text-center">
        {{ $data->links() }}
    </div>

    <div id="m-md" class="modal" data-backdrop="true" style="display: none;">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'form-save']) }}
                        <div class="modal-header">
                            <h5 class="modal-title">Select sales to assign item</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="md-check">
                                    <input type="checkbox" class="has-value" name="flashsales[]"
                                           value="{{ user()->username }}">
                                    <i class="green"></i>
                                    Your Store
                                </label>
                            </div>

                            @foreach(user()->flashsalesAsSeller as $flashSale)
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
                            <button type="button" class="btn primary p-x-md" id="btn_save_selected">Save</button>
                            <button type="button" class="m-l-1 btn btn-link" data-dismiss="modal">Cancel</button>
                        </div>
                        {{ Form::close() }}
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

    </div>


    @push('footer-scripts')
    <script>

        $(function () {
            var $selectAllEl = $('#select_all');
            var $selectedItemsEl = $('.selected_items_checkbox');
            var $addSelectedBtnEl = $('#btn_add_selected');
            var $selectedSaveBtnEl = $('#btn_save_selected');
            var $formSaveEl = $('#form-save');

            var href = $addSelectedBtnEl.prop('href');

            $('.table-as-list tbody tr').click(function (event) {
                if (event.target.type !== 'checkbox') {
                    $(':checkbox', this).trigger('click');
                }
            });

            $selectAllEl.change(function (e) {
                e.preventDefault();
                if ($selectAllEl.is(':checked')) {
                    $selectedItemsEl.each(function (i, e) {
                        if (! $(e).is(':checked')) {
                            $(e).prop('checked', true).trigger('change');
                        }
                    });
                } else {
                    $selectedItemsEl.each(function (i, e) {
                        if ($(e).is(':checked')) {
                            $(e).prop('checked', false).trigger('change');
                        }
                    });
                }
            });

            $selectedItemsEl.change(function (e) {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').addClass("highlight_row");
                    $formSaveEl.append('<input type="hidden" name="inventoryids[]" id="inventory_item_id_' + $(this).val() + '" value="' + $(this).val() + '">');
                } else {
                    $(this).closest('tr').removeClass("highlight_row");
                    $formSaveEl.find('#inventory_item_id_' + $(this).val()).remove();
                }
                var $selectedItemsCheckedEls = $('.selected_items_checkbox:checked');
                var totalChecked = $selectedItemsCheckedEls.length;
                if (totalChecked > 0) {
                    $addSelectedBtnEl.show();
                } else {
                    $addSelectedBtnEl.hide();
                    $selectAllEl.prop('checked', false).trigger('change');
                }
            });

            $selectedSaveBtnEl.click(function (e) {
                e.preventDefault();

                var formData = $formSaveEl.serialize();
                var that = $(this);

                that.parent().find('button').addClass('disabled').prop('disabled', true);
                that.html('Processing...');

                $.ajax({
                    url: "{{ apiRoute('inventory.associate.store', [user()->username]) }}",
                    data: formData,
                    type: "POST",
                    dataType: "json"
                })
                        .done(function (json) {
                            window.location.href = window.location.href;
                        })
                        .fail(function (xhr, status, errorThrown) {
                            alert("Sorry, there was a problem! Please try again.");
                            that.parent().find('button').removeClass('disabled').prop('disabled', false);
                            that.html('Save');
                        });
            });

        });
    </script>
    @endpush

@endsection