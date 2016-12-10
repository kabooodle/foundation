@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Subscription Plan</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p class="m-b-0">Using {{ env('APP_NAME') }} to browse items and submit claims is free.  If you wish to have access to merchant inventory tools, including a b c, we offer various subscription plans.</p>
        </div>
    </div>

    @if($subscription && $subscription->cancelled())
        <div class="box info">
            <div class="box-body clearfix">
                <p class="m-b-0">You cancelled your subscription on <strong>{{ $subscription->updated_at->format('l, F jS \a\t h:ia') }}.</strong></p>
                @if($subscription->onGracePeriod())
                    <p class="m-b-0">You'll still be able to access your account until <strong>{{ $subscription->ends_at->format('l, F jS \a\t h:ia') }}</strong>.</p>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-6">
                    @include('profile.subscription.partials._plan', [
                    '_plan' => \Kabooodle\Models\Plans::getMerchantPlanGroup(),
                    '_disable' => false
                    ])
                </div>
                <div class="col-sm-6">
                    @include('profile.subscription.partials._plan', [
                    '_plan' => \Kabooodle\Models\Plans::getMerchantPlusPlanGroup(),
                    '_disable' => false
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection