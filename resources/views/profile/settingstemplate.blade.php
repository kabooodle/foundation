@extends('layouts.body_w_leftnav', ['contentId' => 'profile_settings'])

@section('body-content-left-nav')
    @include('profile.partials._leftnav')
@endsection

@section('body-inner-content')

    <div class="clearfix">
        @yield('settings-content')
    </div>

@endsection