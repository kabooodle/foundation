@extends('layouts.full', ['contentId' => 'inventory_item_show'])


@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            @if(user() && $item->owner->id == user()->id)
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ $item->sales->count() }} <span class="text-muted">Total Sales</span></span>
            @endif
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ $item->pageViews->count() }} <span class="text-muted">Total Views</span></span>
        </div>
        <div class="btn-toolbar pull-right">
            @if(! $item->canSatisfyRequestedQuantityOf(1))
                <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">Out of stock!</a>
                </div>
            @else
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
            @endif
                <a href="javascript:;" data-toggle="modal" data-target="#share--modal" class="btn-sm btn white">Share</a>
            @if ($item->user_id == user()->id)
                <span class="b-l m-l m-r"></span>
                <a href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm default white"><i class="fa fa-cog" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>

@endsection


@section('body-content')

    @include('inventory.partials._show', ['item' => $item])

    @include('inventory.partials._claimmodal', [
       'post' => route('shop.inventory.claim', [$item->user->username, $item->getUUID()]),
       'redirect' => route('shop.inventory.index', [$item->user->username])
    ])

    @include('comments.container', [
       'comment_model' => $item,
       'comment_index_route' => apiRoute('inventory.comments.index', [$item->id]),
       'comment_post_route' => apiRoute('inventory.comments.store', [$item->id])
    ])

    <inventory-share
            shoppable_save_endpoint="{{ apiRoute('listings.shoppablelink.store') }}"
            shoppable_endpoint="{{ route('externalclaim.shoppable.show', ['::0::', $item->getUUID()]) }}"
            shoppable_id="{{ $item->id }}"
            shoppable_uuid="{{ $item->getUUID() }}"
    ></inventory-share>

@endsection


@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/inventory-show.js') }}"></script>
    <script>
        $(function(){
            clippy('[data-clipboard-target]');
        });
    </script>
@endpush


@push('utilities')
<pageviewstracker
        route="{{ apiRoute('inventory.pageviews.store') }}"
        resource_hash="{{ $item->makeHashedResourceString() }}"
></pageviewstracker>
@endpush