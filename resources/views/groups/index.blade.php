@extends('layouts.full')

@section('body-menu')
    @include('groups.partials._bodymenu')
@endsection

@section('body-content')


        <div class="row">
        @foreach($data as $item)
                <div class="col-md-3">
                    <div class="box p-a-xs">
                        <div class="item">
                            <div class="item-overlay active p-a">
                                {{--<div class="pull-right">--}}
                                    {{--@if($item->is_followed)--}}
                                    {{--<span class="label dark-white text-color"><i class="fa fa-check-square" aria-hidden="true"></i></span>--}}
                                    {{--@else--}}
                                        {{--<span class="label dark-white text-color"><i class="fa fa-plus-square text-warning" aria-hidden="true"></i></span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                                <a href="" class="pull-left text-u-c label label-md success">{{ $item->privacy }}</a>
                            </div>
                            <img src="https://placekitten.com/g/32/20" class="img-responsive">
                        </div>
                        <div class="p-a">
                            <div class="text-muted clearfix">
                                <h6><a href="{{route('groups.show', [$item->getUUID()])}}"  class="_800">{!! $item->name !!}</a></h6>
                                <div>
                                    <span class="_600">{{ $item->allMembers()->count() }}</span> {{ str_plural('member', $item->allMembers()->count())  }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / listItem -->
            @endforeach
        </div>
        <!-- / list -->

    {{ $data->links() }}

@endsection