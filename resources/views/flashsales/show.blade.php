@extends('layouts.body_w_leftnav')


@section('body-menu')
    @include('flashsales.partials._bodymenu')
@endsection


@section('body-content-left-nav')
    @include('flashsales.partials._leftnav')
@endsection

@section('body-inner-content')


    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    @include('flashsales.partials._flashsaleheader')

    <div id="about" class="anchor" style=" display: block;
    position: relative;
    top: -110px;
    visibility: hidden;"></div>

    <div class="">
        <h5>About</h5>
        <div class="text-muted text">
            {!! nl2br($item->description) !!}
        </div>
    </div>

@endsection