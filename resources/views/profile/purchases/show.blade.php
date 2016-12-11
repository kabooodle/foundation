@extends('layouts.full')

@section('body-content')

<div class="box white">
    <div class="row-col m-b">
        <div class="col-md-6">
            <div class="box no-shadow white p-a">
                <div id="item-{{$claim->id}}-carousel" class="carousel image-carousel-container slide" data-ride="carousel">
                    <div class="carousel-outer">
                        <div class="carousel-inner" role="listbox">
                            @foreach($claim->inventoryItem->files as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : null }}" >
                                    <img
                                            data-toggle="lightbox"
                                            data-remote="{{ $image->location }}"
                                            data-gallery="gallery"
                                            src="{{ $image->location }}"
                                    >
                                </div>
                            @endforeach
                        </div>
                        <a class="left carousel-control" href="#item-{{$claim->id}}-carousel" role="button" data-slide="prev">
                            <span class="icon-prev" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#item-{{$claim->id}}-carousel" role="button" data-slide="next">
                            <span class="icon-next" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <ol class="carousel-indicators">
                        @foreach($claim->inventoryItem->files as $key=>$image)
                            <li data-target="#item-{{$item->id}}-carousel" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : null }}">
                                <img src="{{ $image->location }}" style="width: 64px; height: 64px;">
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box-header no-shadow">
                <h2><span class="_800">{!! $claim->inventoryItem->name !!}</span></h2>
                <p class="block m-t-0"><span class="text-muted">Size:</span> {!! $claim->inventoryItem->name !!}</p>
                <p class="m-b-0 m-t-1 h4 text-warning _500">${{ $claim->price }}</p>
            </div>
            <div class="box-body">
                <p class="text-muted text">Claimed on: {{ $claim->created_at->format('F jS \\a\\t g:i A') }}</p>
                <p class="m-b-lg text-muted text">{!! nl2br($claim->inventoryItem->description) !!}</p>
            </div>
        </div>
        <div class="col-md-2 b-l no-shadow">

            <div class=" p-a-md">
                <h6 class="text-muted">Categories</h6>
                @if($claim->inventoryItem->categories->count() > 0)
                    @foreach($claim->inventoryItem->categories as $tag)
                        <span class="label">{{ $tag->name }}</span>
                    @endforeach
                @else
                    <small class="text-muted text-sm"><em>None</em></small>
                @endif
            </div>

            <div class="text-center">
                <small class="text-muted"><i class="fa fa-flag" aria-hidden="true"></i> Flag</small>
            </div>
        </div>
    </div>
</div>

<div class="p-b-3">
    <div class="box">
        <div class="row-col m-b">
            <div class="col-md-6">
                <div class="box-header">
                    <h4>Seller Information</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <p>Name: {{ $claim->inventoryItem->owner->name }}</p>
                    <p>Email: {{ $claim->inventoryItem->owner->email }}</p>
                    <p>Email: {{ $claim->inventoryItem->owner->email }}</p>
                </div>
            </div>
            <div class="col-md-6 b-l no-shadow">
                <div class="box-header">
                    <h4>Shipping Information</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <p>Email: {!! $claim->present()->getShippingStatus() !!}</p>
                    <p>Ship to: {{ $claim->inventoryItem->owner->email }}</p>
                    <p>Ship date: {{ $claim->inventoryItem->owner->email }}</p>
                    <p>Ship date: {{ $claim->shipments }}</p>
                </div>
        </div>
    </div>
</div>

@endsection