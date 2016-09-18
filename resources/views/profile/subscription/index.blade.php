@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Subscription Plan</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p>Using {{ env('APP_NAME') }} to browse items and submit claims is free.  If you wish to have access to merchant inventory tools, including a b c, we offer various subscription plans.</p>
            <p>Currently, you are subscribed to exdfasdfasdf asdfsad</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row no-gutter">
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-body text-center dker">
                            <h6 class="text-u-c m-a-0 m-t">Startup</h6>
                            <h3 class="m-a-0 m-l m-v">
                                <sup>$</sup>
                                <span class="text-2x">39</span>
                                <span class="text-xs">/ mo</span>
                            </h3>
                        </div>
                        <ul class="list b-t b-b m-a-0 no-radius">
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Email preview on air
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Spam testing and blocking
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> 100 GB Space
                                </div></li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> 200 user accounts
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-remove text-danger m-r-xs"></i> Free support for two years
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-remove text-danger m-r-xs"></i> Free upgrade for one year
                                </div>
                            </li>
                        </ul>
                        <div class="text-center p-a-md">
                            <a href="" class="btn btn-block btn-lg white disabled" disabled>Current</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-body text-center dker">
                            <h6 class="text-u-c m-a-0 m-t">Enterprise</h6>
                            <h3 class="m-a-0 m-l m-v">
                                <sup>$</sup>
                                <span class="text-2x">99</span>
                                <span class="text-xs">/ mo</span>
                            </h3>
                        </div>
                        <ul class="list b-t b-b m-a-0 no-radius">
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Email preview on air
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Spam testing and blocking
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> 100 GB Space
                                </div></li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> 200 user accounts
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Free support for two years
                                </div>
                            </li>
                            <li class="list-item">
                                <div class="list-body">
                                    <i class="fa fa-check text-success m-r-xs"></i> Free upgrade for one year
                                </div>
                            </li>
                        </ul>
                        <div class="text-center p-a-md">
                            {{ Form::open(['route' => ['profile.subscription.store', 'p=kabooodle_merchant_plan']]) }}
                            <button type="submit" class="btn btn-block btn-lg white">Upgrade</button>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection