@extends('layouts.body_w_leftnav')

@section('body-menu')

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

        <div class="btn-group dropdown " style="display: none" id="btngroup-bulk" >
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Bulk</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Delete</a>
                <a id="btn_add_selected" href="{{ route('shop.inventory.associate.create', [user()->username]) }}?" class="dropdown-item">Add Selected Items to Sale</a>
            </div>
        </div>



    </div>
</div>


    <div class="pull-right">
        <a href="{{ route('shop.inventory.index', [user()->username]) }}" class="btn btn-sm white">Manage</a>
        <a href="{{ route('shop.inventory.create', [user()->username]) }}" class="btn btn-sm white">Add Items</a>
    </div>

@endsection


@section('body-content')

    <table class="table table-condensed table-as-list white">
        <thead>
        <tr>
            <th><div style="padding-left: 12px !important;"><input id="select_all" type="checkbox" class=""></div></th>
            <th class="text-muted">Details</th>
            <th class="text-muted">*In-stock</th>
            <th class="text-muted">Price</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important" valign="middle" class="">
                    <div class="list-item p-r-0 @if($item->flashsales->count() > 0) b-success b-l b-l-2x @endif ">
                        <input type="checkbox" class="selected_items_checkbox" name="selected_items[]" value="{{ $item->id }}">
                    </div>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important">
                    <div class="list white">
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
                                <div class="text-ellipsis text-muted text-sm">Categories: @foreach($item->categories as $cat) {{ $cat->name }} @endforeach</div>
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
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important" valign="middle">
                    <span class="h5">{{ $item->current_qty }}</span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important" valign="middle">
                    <span class="h5 ">${{ $item->price_usd }}</span>
                </td>
                <td style="padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important" valign="middle">
                    <div class="text-right text-muted p-r-1">
                        <a href="{{ route('shop.inventory.show', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}" class="btn white btn-sm _400">View</a>                    <a href="{{ route('shop.inventory.edit', [$item->owner->username, $item->obfuscateToURIStringFromModel()]) }}" class="btn white btn-sm _400">Edit</a>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Large modal</h5>
                </div>
                <div class="modal-body text-center p-lg">
                    <p>Are you sure to execute this action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div>


    @push('footer-scripts')
    <script>

            var JQUERY4U = {};
            var W = window;

            JQUERY4U.UTIL = {
                /**
                 * Add a parameter to url if doesn't already exist
                 * @param param - the parameter to add
                 * @param value - the value of the parameter
                 * @return url - the url with the appended parameter
                 */
                addParamToUrl: function(param, value) {
                    //check if param exists
                    var result = new RegExp(param + "=([^&]*)", "i").exec(W.location.search);
                    result = result && result[1] || "";

                    //added seperately to append ? before params
                    var loc = W.location;
                    var url = loc.protocol + '//' + loc.host + loc.pathname + loc.search;

                    //param doesn't exist in url, add it
                    if (result == '') {
                        //doesn't have any params
                        if (loc.search == '') {
                            url += "?" + param + '=' + value;
                        } else {
                            url += "&" + param + '=' + value;
                        }
                    }

                    return url;
                }
            };


        $(function(){
            var $selectAllEl = $('#select_all');
            var $selectedBtnEl = $('#btngroup-bulk');
            var $selectedItemsEl = $('.selected_items_checkbox');
            var $addSelectedBtnEl =  $('#btn_add_selected');

            var href = $addSelectedBtnEl.prop('href');

            $selectAllEl.change(function(e){
                e.preventDefault();
                if ($selectAllEl.is(':checked')) {
                    $selectedItemsEl.each(function(i,e){
                        $(this).prop('checked', true).trigger('change');
                    });
                } else {
                    $selectedItemsEl.each(function(i,e){
                        $(this).prop('checked', false).trigger('change');
                    });
                }
            });

            $selectedItemsEl.change(function(e){
                var $selectedItemsCheckedEls = $('.selected_items_checkbox:checked');
                var totalChecked = $selectedItemsCheckedEls.length;
                if (totalChecked > 0 ) {
                    $selectedBtnEl.show();
//                    $addSelectedBtnEl.prop('href', JQUERY4U.UTIL.addParamToUrl('selected_items[]', $(this).val()));
                } else {
                    $selectedBtnEl.hide();
                    $selectAllEl.prop('checked', false).trigger('change');
                }
            });
        });
    </script>
    @endpush

@endsection