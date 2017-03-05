@extends('layouts.full')

@section('body-content')
    <div class="row">
        <div class="col-md-3">
            <div class="hidden-xs-down" id="inner-left-menu">
                <div class="nav-active-primary white">
                    <ul class="nav nav-pills nav-sm">
                        @yield('body-content-left-nav')
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <button class="btn btn-sm white pull-right hidden-sm-up" ui-toggle-class="show" target="#inner-left-menu">
                <i class="fa fa-bars"></i>
            </button>
            @yield('body-inner-content')
        </div>
    </div>
@endsection