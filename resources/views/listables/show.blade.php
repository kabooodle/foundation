@extends('layouts.full', ['contentId' => 'listable_item_show'])


@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            @if(webUser() && $listable->owner->id == webUser()->id)
                <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ $listable->sales->count() }} <span class="text-muted">Total Sales</span></span>
            @endif
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ $listable->views->count() }} <span class="text-muted">Total Views</span></span>
        </div>
        <div class="btn-toolbar pull-right">
            @if(! $listable->canSatisfyRequestedQuantityOf(1))
                <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
                    <a class="btn btn-sm claim  _800 disabled" disabled href="#">Out of stock!</a>
                </div>
            @else
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="{{ $listable->getEditRoute() }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
            @endif
                <a href="javascript:;" data-toggle="modal" data-target="#share--modal" class="btn-sm btn white">Share</a>
            @if($listable->user_id == webUser()->id)
                <span class="b-l m-l m-r"></span>
                <a href="{{ $listable->getEditRoute() }}" class="btn btn-sm default white"><i class="fa fa-cog" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>

@endsection


@section('body-content')

    @include('listables.partials._show', ['listable' => $listable])

    @include('listables.partials._claimmodal', [
       'post' => route('shop.inventory.claim', [$listable->user->username, $listable->getUUID()]),
       'redirect' => route('shop.inventory.index', [$listable->user->username])
    ])

    @include('comments.container', [
       'comment_model' => $listable,
       'comment_index_route' => apiRoute('listables.comments.index', [$listable->user->username, $listable->id]),
       'comment_post_route' => apiRoute('listables.comments.store', [$listable->user->username, $listable->id]),
    ])

    <listable-share
        shoppable_save_endpoint="{{ apiRoute('listings.shoppablelink.store') }}"
        shoppable_endpoint="{{ route('externalclaim.shoppable.show', ['::0::', $listable->getUUID()]) }}"
        shoppable_id="{{ $listable->id }}"
        shoppable_uuid="{{ $listable->getUUID() }}"
    ></listable-share>

@endsection


@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/listables-show.js') }}fnz"></script>
@endpush


@push('utilities')
<pageviewstracker
        route="{{ apiRoute('views.store') }}"
        resource_hash="{{ $listable->makeHashedResourceString() }}"
></pageviewstracker>
@endpush