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

        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Bulk</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Delete</a>
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
            <th></th>
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
                        <input type="checkbox">
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





@endsection