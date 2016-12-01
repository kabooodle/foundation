@extends('layouts.full')


@section('body-menu')
    <div class="clearfix">
        <div class="pull-left">
            @if($listingItem->owner->id == user()->id)
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ $listingItem->sales->count() }} <span class="text-muted">Sales</span></span>
            @endif
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ $listingItem->pageViews->count() }} <span class="text-muted">Views</span></span>
        </div>
        <div class="btn-toolbar pull-right">
            <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x {{ $listingItem->inventoryItem->is_liked ? 'warning' : null }}"></i> {{ $listingItem->inventoryItem->likes->count() }} Likes</a>
            <a href="" class="btn-sm btn white">Share</a>
            @if(! $listingItem->inventoryItem->canSatisfyRequestedQuantityOf(1))
                <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">Out of stock!</a>
                </div>
            @else
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="" class="btn btn-sm claim  _800 ">Claim Item</a>
            @endif
        </div>
    </div>
@endsection


@section('body-content')

    <div class="box white">
        <div class="box-body">
            {{ $listingItem->type }}
        </div>
    </div>

    @include('inventory.partials._show', [
        'item' => $listingItem->inventoryItem
    ])

    @include('inventory.partials._claimmodal', [
        'post' => apiRoute('listings.listingitems.claims.store', [$listingItem->listing_id, $listingItem->id]),
        'redirect' => ('')
    ])

    @include('comments.container', [
        'comment_model' => $listingItem->inventoryItem,
        'comment_index_route' => apiRoute('inventory.comments.index', [$listingItem->inventoryItem->id]),
        'comment_post_route' => apiRoute('inventory.comments.store', [$listingItem->inventoryItem->id])
    ])
@endsection


@push('utilities')
<pageviewstracker
        route="{{ apiRoute('inventory.pageviews.store') }}"
        resource_hash="{{ $listingItem->makeHashedResourceString() }}"
></pageviewstracker>
@endpush