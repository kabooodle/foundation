@extends('layouts.full')

@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush


    <div class="box">
        <div class="box-header">
            <h5>Associate inventory to sales</h5>
        </div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th class="text-muted">Details</th>
                    <th class="text-muted">*In-stock</th>
                    <th class="text-muted">Price</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $item)
                    <tr>
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
                            <div class="pull-right text-muted p-r-1">
                                <button class="btn white warning">Add To A Sale</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="row">
                                <div class="col-md-9">
                                    {{ Form::select('', [] , []) }}
                                </div>
                                <div class="col-md-2">
                                    {{ Form::select('', [], []) }}
                                </div>
                                <div class="col-md-1">
                                   <i class="fa fa-times pull-right text-muted"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <script>
        $(function(){

            $('select').selectize({
                plugins: ['restore_on_backspace', 'remove_button'],
                delimiter: ',',
                persist: false
            });
        });
    </script>

@endsection