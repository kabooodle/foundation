@extends('layouts.full')

@section('body-menu')
    @include('flashsales.shop._bodymenu')
@endsection


@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    @include('flashsales.partials._flashsaleheader_mini')

    @include('inventory.partials._show', ['item' => $inventory])

@endsection