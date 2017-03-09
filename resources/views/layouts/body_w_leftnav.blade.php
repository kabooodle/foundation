@extends('layouts.full')

@section('body-content')
    <div class="clearfix ">
        <button class="btn btn-sm pull-right white hidden-md-up" data-toggle="collapse" ui-toggle-class="show" data-target="#inner-left-menu">
            <i class="fa fa-bars"></i>
        </button>
    </div>
    <div class="row">
        <div class="col-md-3 m-b-1">
            <div class="hidden-xs-down" id="inner-left-menu">
                <div class="nav-active-primary white">
                    <ul class="nav nav-pills nav-sm">
                        @yield('body-content-left-nav')
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            @yield('body-inner-content')
        </div>
    </div>
@endsection