@extends('layouts.body_w_leftnav', ['contentId' => 'messenger'])

@section('body-content-left-nav')
    @include('messenger.partials._leftnav')
@endsection

@section('body-menu')

@endsection

@section('body-inner-content')

    <div class="clearfix">
        @yield('settings-content')
    </div>

@endsection