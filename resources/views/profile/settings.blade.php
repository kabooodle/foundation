@extends('layouts.full')

@section('body-content')
    <div class="box">
        <div class="padding">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="list-body clearfix">
                        <div class="pull-left m-r-1">
                            <img src="{{ staticAsset('/assets/images/icons/FB-f-Logo__blue_50.png') }}">
                        </div>
                        <div class="pull-left">
                            <span class="h6 _500">Facebook Connect</span><br>
                            <span class="text-muted">Post inventory to facebook groups you admin.</span>
                        </div>
                        <div class="pull-right">
                            @if (webUser()->fbTokenExpired())
                            <a href="{{ Facebook::getLoginUrl() }}" class="btn white btn-sm">Connect</a>
                            @else
                            <a href="/social/facebook/revoke" class="btn white btn-sm">Revoke</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection