@extends('layouts.full', ['contentId' => 'listing-item-page'])

@section('body-menu')
    <div class="clearfix">
        <div class="pull-left">
            @if(user() && $listingItem->owner->id == user()->id)
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ $listingItem->sales->count() }} <span class="text-muted">Sales</span></span>
            @endif
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ $listingItem->pageViews->count() }} <span class="text-muted">Views</span></span>
        </div>
        <div class="btn-toolbar pull-right">

            @if(! $listingItem->inventoryItem->canSatisfyRequestedQuantityOf(1))
                <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">
                        @if($listingItem->inventoryItem->getOnHoldQuantity())
                            On hold!
                        @else
                            Out of stock!
                        @endif
                    </a>
                </div>
            @else
                @if($listingItem->claimableBasedOnSchedule())
                    <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="" class="btn btn-sm claim  _800 ">Claim Item</a>
                @else
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">Not Yet Claimable</a>
                @endif
            @endif
            <followable
                    able_name="watchable"
                    able_type="{{ get_class($listingItem) }}"
                    able_id="{{ $listingItem->id }}"
                    unfollow_text="Watching"
                    follow_text="Watch"
                    btn_size_class="btn-sm"
                    already_following="{{ $listingItem->is_watched ? 'true' : 'false' }}"
                    endpoint="{{ apiRoute('listings.listingitems.watchers.store', [$listingItem->listing_id, $listingItem->id]) }}"
            >
            </followable>
        </div>
    </div>
@endsection


@section('body-content')

        <div class="box white">
            <div class="box-header clearfix">
                <h4 class="">
                    <span class="pull-left">
                        @include('listings._listingtype', ['_type' => $listingItem->type]) {{ $listingItem->sale_name }} - {{ $listingItem->getNameOfResource() }}
                        @if($listingItem->type == 'flashsale')
                            <a href="{{ route('flashsales.show', [$listingItem->flashsale->uuid] ) }}"class="m-t-xs block link btn-link text-primary">Back to flash sale</a>
                        @else
                            <a href="{{ route('listings.show', [$listingItem->listing->uuid] ) }}"class="m-t-xs block link btn-link text-primary">More items from {{ $listingItem->owner->username }}</a>
                        @endif
                    </span>
                    @if($listingItem->type == 'flashsale')
                        <span class="pull-right m-b-0 text-muted text-sm">Items can be claimed {{ $listingItem->flashsale->claimable_range }}</span>
                    @else
                        <span class="pull-right m-b-0 text-muted text-sm">Items can be claimed {{ $listingItem->listing->claimable_range }}</span>
                    @endif
                </h4>
            </div>
        </div>

    @include('inventory.partials._show', [
        'item' => $listingItem->inventoryItem
    ])

    @include('inventory.partials._claimmodal', [
        'post' => apiRoute('listings.listingitems.claims.store', [$listingItem->listing_id, $listingItem->id]),
        'guestClaimEndpoint' => apiRoute('listings.listingitems.claims.guest-store', [$listingItem->listing_id, $listingItem->id]),
        'redirect' => Request::url()
    ])

    @include('comments.container', [
        'comment_model' => $listingItem->inventoryItem,
        'comment_index_route' => apiRoute('inventory.comments.index', [$listingItem->inventoryItem->id]),
        'comment_post_route' => apiRoute('inventory.comments.store', [$listingItem->inventoryItem->id])
    ])
@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listing-items-page.js') }}"></script>
@endpush

@push('utilities')
<pageviewstracker
        route="{{ apiRoute('inventory.pageviews.store') }}"
        resource_hash="{{ $listingItem->makeHashedResourceString() }}"
></pageviewstracker>
@endpush