@extends('layouts.full')


@if($data->count() > 0)
@section('body-menu')
    <div class="btn-toolbar center-block text-center">
        <div class="btn-group">
            <a href="{{ route('flashsales.create') }}" class="btn btn-sm primary pull-left" >Create New</a>

        </div>
        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Filter</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Active</a>
                <a class="dropdown-item" href="">Archived</a>
            </div>
        </div>
    </div>
@endsection
@endif

@section('body-content')

        @if($data->count() > 0)

            <div class="row">
                @foreach($data as $sale)
                    <div class="col-md-4">
                        <div class="box p-a-xs">
                            <div class="item">
                                <div class="item-overlay active p-a">
                                    {{--<div class="pull-right">--}}
                                        {{--<span class="label dark-white text-color"><i class="fa  @if($sale->hostIsGroup()) fa-users @else fa-user @endif" aria-hidden="true"></i></span>--}}
                                    {{--</div>--}}
                                    <a href="{{ route('flashsales.show', [$sale->getUUID()]) }}" class="pull-left text-u-c label label-md">{{ $sale->active? 'Active' : 'Unpublished' }}</a>
                                </div>
                                <img src="https://placekitten.com/g/32/20" class="img-responsive">
                            </div>
                            <div class="p-a clearfix">
                                <div class="text-muted ">
                                    <span class="">{{ $sale->starts_at->diffForHumans() }}, {{ $sale->starts_at->format("M d \\@ h:ia") }}</span>
                                </div>
                                <div class="m-b-0 h-2x"><a href="{{ route('flashsales.show', [$sale->getUUID()]) }}" class="_800">{{ $sale->name }}</a></div>
                                {{--{{ str_limit(nl2br($sale->description), 100) }}--}}
                                <div class="text-xs pull-right">
                                <a href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->likes->count() }}</span> <span class="text-muted">Followers</span></a>
                                    <a class="m-l-1" href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->sellers->count() }}</span>  <span class="text-muted">Sellers</span></a>
                                    <a class="m-l-1" href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->inventoryItems->count() }}</span>  <span class="text-muted">Items</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="white box padding">
                        <div class="row">
                            <div class="col-md-7">
                                <img src="/assets/images/online-shop.jpg" class="" width="500">
                            </div>
                            <div class="col-md-5">
                                <div class="center-block text-center">
                                    <h3 style="margin: 50% 0;"><a href="{{ route('flashsales.create') }}" class="btn btn-lg success">Create New Flash Sale</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

@endsection