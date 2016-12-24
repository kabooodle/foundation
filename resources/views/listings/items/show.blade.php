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
                    already_following="{{ $listingItem->is_watched ? 1 : 0 }}"
                    endpoint="{{ apiRoute('listings.listingitems.watchers.store', [$listingItem->listing_id, $listingItem->id]) }}"
            >
            </followable>
        </div>
    </div>
@endsection


@section('body-content')

        <div class="box white">
            <div class="box-header">
                <h4>{{ $listingItem->sale_name }} @include('listings._listingtype', ['_type' => $listingItem->type])</h4>
            </div>
            @if(! $listingItem->claimableBasedOnSchedule())
            <div class="box-divider"></div>
            <div class="box-body clearfix">
                <p class="pull-left m-b-0">Scheduled to list on: {{ $listingItem->listing->scheduled_for }}</p>
                <p class="pull-right m-b-0">Claimable after: {{ $listingItem->listing->claimableAfter()->format('j m-d') }}</p>
            </div>
            @endif
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