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

            <a data-toggle="modal" data-target="#m-md"
               data-backdrop="static" data-keyboard="false" href="#" disabled class="disabled btn_add_selected text-white  btn btn-sm warning">Add Selected
                Items to Sale</a>

            <a data-toggle="modal" data-target="#m-md-fb"
               data-backdrop="static" data-keyboard="false" href="#" disabled class="disabled text-white btn_add_selected btn btn-sm warning">Add Selected
                Items to Facebook</a>

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
            <th class="text-muted p-l-0 m-l-0">Price</th>
            <th class="text-muted p-l-0 m-l-0">Claims</th>
            <th class="text-muted p-l-0 m-l-0">*Available Qty</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr data-id="{{ $item->id }}">
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle" class="">
                    <div class="list-item p-r-0 ">
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
                                                class="label label-xs outline text-u-c">{!! $tag->tag_name !!}</span> @endforeach
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
                    <span class="h5 ">${{ $item->price_usd }}</span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle">
                    <span class="h5 "><a href="#"><span class="text-success">{{ $item->acceptedClaims->count() }}</span></a> <span class="text-muted">|</span> <a href="#"><span class="text-warning">{{ $item->pendingClaims->count() }}</span></a></span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important"
                    valign="middle">
                    <span class="h5">{{ $item->getAvailableQuantity() }}</span>
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
            @if($item->flashsales->count() > 0)
                <tr data-id="{{ $item->id }}">
                    <td class="b-t-0 b-" style="border-top: 0"></td>
                    <td colspan="5" class="b-t-0" style="border-top: 0">
                        <div class="flashsale_wrapper">
                            <span class="text-muted">Flash sales:</span>
                            @foreach($item->flashsales as $flashsale)
                                <span class="label outline">{{ $flashsale->name }} <a data-toggle="tooltip"
                                                                              data-route="{{ apiRoute('inventory.associate.destroy', [user()->username, $flashsale->pivot->id]) }}"
                                                                              data-placement="top"
                                                                              title="Remove from sale"
                                                                              class="m-l btn_remove_fromflashsale"
                                                                              data-item-id="{{ $item->id }}"
                                                                              data-flashsale-id="{{ $flashsale->id }}"
                                                                              href="#"><i
                                                class="fa fa-times"></i></a></span>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endif

            @if($item->facebooksales->count() > 0)
                <tr data-id="{{ $item->id }}">
                    <td class="b-t-0 b-" style="border-top: 0"></td>
                    <td colspan="5" class="b-t-0" style="border-top: 0">
                        <div class="flashsale_wrapper">
                            <span class="text-muted">Facebook Albums:</span>
                            @foreach($item->facebooksales as $facebooksale)
                                <span class="label outline">{{ $facebooksale->facebook_post_id }} <a data-toggle="tooltip"
                                                                                      data-route="{{ apiRoute('inventory.associate.destroy', [user()->username, $facebooksale->id]) }}"
                                                                                      data-placement="top"
                                                                                      title="Remove from sale"
                                                                                      class="m-l btn_remove_fromflashsale"
                                                                                      data-item-id="{{ $item->id }}"
                                                                                      data-flashsale-id="{{ $facebooksale->id }}"
                                                                                      href="#"><i
                                                class="fa fa-times"></i></a></span>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endif

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
                        {{ Form::open(['class' => 'form-save']) }}
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
                            <button type="button" class="m-l-1 btn btn-link" data-dismiss="modal">Cancel</button>
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
            var $addSelectedBtnEl = $('.btn_add_selected');
            var $selectedSaveBtnEl = $('.btn_save_selected');
            var $formSaveEl = $('.form-save');

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
                        if (!$(e).is(':checked')) {
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
                    $(this).closest('tr').parent().find('tr[data-id="' + $(this).val() + '"]').addClass("highlight_row");
                    $formSaveEl.append('<input type="hidden" name="inventoryids[]" id="inventory_item_id_' + $(this).val() + '" value="' + $(this).val() + '">');
                } else {
                    $(this).closest('tr').parent().find('tr[data-id="' + $(this).val() + '"]').removeClass("highlight_row");
                    $formSaveEl.find('#inventory_item_id_' + $(this).val()).remove();
                }
                var $selectedItemsCheckedEls = $('.selected_items_checkbox:checked');
                var totalChecked = $selectedItemsCheckedEls.length;
                if (totalChecked > 0) {
                    $addSelectedBtnEl.removeClass('disabled').prop('disabled', false);
                } else {
                    $addSelectedBtnEl.addClass('disabled').prop('disabled', true);
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

            var x = $('.btn_remove_fromflashsale');

            x.click(function (e) {
                e.preventDefault();
                var that = $(this);

                that.tooltip('dispose');
                that.addClass('disabled').attr('disabled', true).unbind('click');
                that.html('<i class="fa fa-spinner fa-spin"></i>');

                noty({
                    text: 'Are you sure? This will remove the item from the flash sale',
                    layout: 'center',
                    theme: 'relax',
                    type: 'alert',
                    modal: true,
                    animation: {
                        open: {height: 'toggle'},
                        close: {height: 'toggle'},
                        easing: 'linear',
                        speed: 1
                    },
                    timeout: 9000,
                    buttons: [
                        {
                            addClass: 'btn btn-sm primary b-primary', text: 'Ok', onClick: function ($noty) {
                            $noty.close();

                            $.ajax({
                                url: that.attr('data-route'),
                                type: "DELETE",
                                dataType: "json"
                            })
                                    .done(function (json) {
                                        var wrapper = that.closest('.flashsale_wrapper');
                                        that.closest('.label').remove();
                                        if (wrapper.find('.label').length == 0) {
                                            wrapper.closest('tr').remove();
                                        }
                                    })
                                    .fail(function (xhr, status, errorThrown) {
                                        alert("Sorry, there was a problem! Please try again.");
                                        that.parent().find('button').removeClass('disabled').prop('disabled', false);
                                        that.html('<i class="fa fa-times"></i>');
                                        that.bind('click', true);
                                    });
                        }
                        },
                        {
                            addClass: 'btn btn-link white btn-sm', text: 'Cancel', onClick: function ($noty) {
                            $noty.close();
                            that.parent().find('button').removeClass('disabled').prop('disabled', false);
                            that.html('<i class="fa fa-times"></i>');
                            that.bind('click', true);
                        }
                        }
                    ]
                });


            });

        });
    </script>
    @endpush

@endsection