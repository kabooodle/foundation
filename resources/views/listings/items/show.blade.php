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
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="" class="btn btn-sm claim  _800 ">Claim Item</a>
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

    @if(user() && $listingItem->owner->id == user()->id)
        <div class="box white">
            <div class="box-body">
                {{ $listingItem->type }}
            </div>
        </div>
    @endif

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
<script src="/assets/js/listing-items-page.js"></script>
@endpush

@push('utilities')
<pageviewstracker
        route="{{ apiRoute('inventory.pageviews.store') }}"
        resource_hash="{{ $listingItem->makeHashedResourceString() }}"
></pageviewstracker>
@endpush