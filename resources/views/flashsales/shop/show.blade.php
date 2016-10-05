@extends('layouts.full')

@section('body-menu')
    @include('flashsales.shop._bodymenu')
@endsection


@section('body-content')

    @include('flashsales.partials._flashsaleheader_mini')

    @include('inventory.partials._show', ['item' => $inventory])

    @include('inventory.partials._claimmodal', ['post' => route('flashsales.shop.claim', [$item->getUUID(), $inventory->getUUID()]) , 'redirect' => route('flashsales.shop.index', [$item->getUUID()])])

    @include('comments.container', ['comment_model' => $inventory, 'comment_post_route' => route('flashsales.shop.comments.store', [$item->getUUID(), $inventory->getUUID()])])

@endsection