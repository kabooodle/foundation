@extends('layouts.full')

@section('body-menu')
    @include('flashsales.partials._bodymenu')
@endsection


@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    @include('flashsales.partials._flashsaleheader')


    @if($item->inventoryItems->count() > 0)
    <div class="row">
        <div class="col-md-3">
            <div class="list-group m-b">
                <a href="" class="list-group-item">
                    <span class="pull-right label">12</span>
                    Carly
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">5</span>
                    Lindsey's
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right text-muted m-l-xs"></span>
                    <span class="pull-right label ">4</span>
                    Socks
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">9</span>
                    Nike
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">10</span>
                    Adidas
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
            @foreach($item->inventoryItems as $inventoryItem)
                    <div class="col-md-4">
                        <div class="box p-a-xs">
                            <div class="item">
                                <img src="https://placekitten.com/g/32/20" class="img-responsive">
                            </div>
                            <div class="p-a">
                                <div class="text-muted clearfix">
                                    <h6 class="m-b-0"><a href="{{ route('flashsales.shop.show', [$item->getUUID(), $inventoryItem->getUUID()]) }}" class="_800">{!! $inventoryItem->name !!}</a></h6>
                                </div>
                            </div>
                        </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>

    @endif
    <hr>
    <div id="about" class="anchor" style=" display: block;
    position: relative;
    top: -110px;
    visibility: hidden;"></div>

    <div class="">
        <h5>About</h5>
        <div class="text-muted text">
            {!! nl2br($item->description) !!}
        </div>
    </div>

@endsection