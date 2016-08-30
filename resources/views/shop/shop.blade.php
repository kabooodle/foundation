@extends('layouts.full')

@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,50) }} Sales</span>
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ rand(0,50) }} Views</span>
        </div>
        <div class="btn-toolbar pull-right">
            <a href="" class="btn-sm btn white"><i class="fa fa-share" aria-hidden="true"></i> Share</a>
            <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x"></i> {{ $user->likes->count() }} Likes</a>
        </div>
    </div>

@endsection

@section('body-content')

    <div class="box white">
        <div class="padding">
            <h4 class="text-center">{!! $user->username !!}</h4>
        </div>
    </div>

@endsection