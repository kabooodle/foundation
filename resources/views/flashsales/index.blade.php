@extends('layouts.full')


@if($data->count() > 0)
@section('body-menu')
    <div class="btn-toolbar center-block text-center">
        <div class="btn-group">
            <a href="{{ route('flashsales.create') }}" class="btn btn-sm primary pull-left" >Create New</a>

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
                                <div class="item-overlay active p-a-xs">
                                    @if($sale->privacy == 'private')
                                    <a href="{{ route('flashsales.show', [$sale->getUUID()]) }}" class="pull-left text-u-c label danger label-md">{{ $sale->privacy }}</a>
                                    @endif
                                        <button type="button" class="pull-right btn-follow btn   white pull-right label dark-white text-color btn-xs">Watch</button>
                                </div>
                               <div class="coverimage FlexEmbed FlexEmbed--3by1" style="background-image: url('{{ $sale->coverimage->location }}')"></div>
                            </div>
                            <div class="p-a-xs clearfix">
                                <div class="text-muted ">
                                    <span class="text-xs">{{ $sale->starts_at->format("M d, g:ia") }} - {{ $sale->ends_at->format("M d, g:ia") }}</span>
                                </div>
                                <div class="m-b-0 h-2x"><a href="{{ route('flashsales.show', [$sale->getUUID()]) }}" class="_800">{{ $sale->name }}</a></div>
                                <div class="text-xs pull-right">
                                <a href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->likes->count() }}</span> <span class="text-muted">Watchers</span></a>
                                    <a class="m-l-1" href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->sellers()->count() }}</span>  <span class="text-muted">Sellers</span></a>
                                    <a class="m-l-1" href="{{ route('flashsales.show', [$sale->getUUID()]) }}"><span class="_600">{{ $sale->listingItems->count() }}</span>  <span class="text-muted">Items</span></a>
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
                                <img src="{{ staticAsset('/assets/images/online-shop.jpg') }}" class="" width="500">
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