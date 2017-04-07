@extends('layouts.full', ['contentId' => 'inventory_grouping_show'])


@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            @if(webUser() && $grouping->owner->id == webUser()->id)
                <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ $grouping->sales->count() }} <span class="text-muted">Total Sales</span></span>
            @endif
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ $grouping->views->count() }} <span class="text-muted">Total Views</span></span>
        </div>
        <div class="btn-toolbar pull-right">
            @if(! $grouping->canSatisfyRequestedQuantityOf(1))
                <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">Out of stock!</a>
                </div>
            @else
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="{{ route('shop.inventory.edit', [$grouping->user->username, $grouping->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
            @endif
            <a href="javascript:;" data-toggle="modal" data-target="#share--modal" class="btn-sm btn white">Share</a>
            @if($grouping->user_id == webUser()->id)
                <span class="b-l m-l m-r"></span>
                <a href="{{ route('shop.inventory.edit', [$grouping->user->username, $grouping->getUUID()]) }}" class="btn btn-sm default white"><i class="fa fa-cog" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>

@endsection


@section('body-content')

    @include('inventory-groupings.partials._show', ['item' => $grouping])

    {{--@include('listables.partials._claimmodal', [--}}
       {{--'post' => route('shop.inventory.claim', [$grouping->user->username, $grouping->getUUID()]),--}}
       {{--'redirect' => route('shop.inventory.index', [$grouping->user->username])--}}
    {{--])--}}

    @include('comments.container', [
       'comment_model' => $grouping,
       'comment_index_route' => apiRoute('inventory-groupings.comments.index', [$grouping->owner->username, $grouping->id]),
       'comment_post_route' => apiRoute('inventory-groupings.comments.store', [$grouping->owner->username, $grouping->id])
    ])

    <inventory-share
            shoppable_save_endpoint="{{ apiRoute('listings.shoppablelink.store') }}"
            shoppable_endpoint="{{ route('externalclaim.shoppable.show', ['::0::', $grouping->getUUID()]) }}"
            shoppable_id="{{ $grouping->id }}"
            shoppable_uuid="{{ $grouping->getUUID() }}"
    ></inventory-share>

@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/inventory-show.js') }}"></script>
@endpush


@push('utilities')
<pageviewstracker
        route="{{ apiRoute('views.store') }}"
        resource_hash="{{ $grouping->makeHashedResourceString() }}"
></pageviewstracker>
@endpush