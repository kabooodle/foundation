@extends('layouts.full')


@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Sales</span></span>
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Views</span></span>

        </div>
        <div class="btn-toolbar pull-right">
                <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
                <a href="" class="btn-sm btn white"><i class="fa fa-share" aria-hidden="true"></i> Share</a>
            <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x {{ $item->is_liked ? 'warning' : null }}"></i> {{ $item->likes->count() }} Likes</a>
            @if ($item->user_id == Auth::id())
                <span class="b-l m-l m-r"></span>
                <a href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm default white"><i class="fa fa-cog" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>

@endsection

@section('body-content')

   @include('inventory.partials._show', ['item' => $item])
   {{--@include('inventory.partials._claimmodal', ['item' => user()])--}}
@endsection