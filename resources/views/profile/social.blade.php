@extends('profile.settingstemplate')


@section('settings-content')


    {{ Form::open(['route' => 'profile.social.update', 'method' => 'POST']) }}

    <div class="box">
        <div class="box-header">
            <h2>Connect your social profiles</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <div class="list-body">
                <div class="pull-left m-r-1">
                    <img src="{{ staticAsset('/assets/images/icons/FB-f-Logo__blue_50.png') }}">
                </div>
                <div class="pull-left">
                    <span class="h6 _500">Facebook</span><br>
                    <span class="text-muted">Login via Facebook, and/or List inventory to facebook groups you admin.</span>
                </div>
                <div class="pull-right">
                    @if (webUser()->fbTokenExpired())
                        <a href="{{ Facebook::getLoginUrl() }}" class="btn white btn-sm">Connect</a>
                    @else
                        <a href="/social/facebook/revoke" class="btn white btn-sm">Disconnect</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="box-body">
            @foreach(['instagram', 'twitter', 'youTube'] as $social)
            <div class="row form-group ">
                <label class="control-label col-sm-3">{{ ucfirst($social) }}</label>
                <div class="col-sm-6">
                    <input class="form-control" value="<?php $val = 'social_'.strtolower($social); echo webUser()->{$val}; ?>" name="social_{{ strtolower($social) }}">
                </div>
            </div>
            @endforeach
                <div class="row form-group ">
                    <label class="control-label col-sm-3">Personal Website</label>
                    <div class="col-sm-6">
                        <input class="form-control"  value="{{ webuser()->social_website }}" name="social_website">
                    </div>
                </div>
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>

    {{ Form::close() }}

@endsection