@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Subscription Plan</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p class="">Using {{ env('APP_NAME') }} to browse items and submit claims is free.  If you wish to have access to merchant inventory tools, including a b c, we offer various subscription plans.</p>
            @if(webUser()->currentSubscription() && webUser()->upcomingInvoice())
                <p class="m-b-0">Your next scheduled billing date is: {{ webUser()->upcomingInvoice()->date()->format('F jS Y') }}, for {{ webUser()->upcomingInvoice()->total() }}.</p>
            @endif
        </div>
    </div>

    @if(webUser()->onGenericTrial())
        <div class="box info">
            <div class="box-body">
                <div class="text-center center-block">
                    <p class="m-b-0">You are currently on a 30 day trial. Your trial ends on <strong>{{ $user->trial_ends_at->format('l, F jS \a\t h:ia') }}</strong></p>
                </div>
            </div>
        </div>
    @endif

    @if($subscription && $subscription->cancelled())
        <div class="box info">
            <div class="box-body clearfix">
                <p class="m-b-0">You cancelled your subscription on <strong>{{ $subscription->updated_at->format('l, F jS Y \a\t h:ia') }}</strong></p>
                @if($subscription->onGracePeriod())
                    <p class="m-b-0">You'll still be able to access your account until <strong>{{ $subscription->ends_at->format('l, F jS Y \a\t h:ia') }}</strong></p>
                @endif
            </div>
        </div>
    @endif

    @if(webUser()->pendingQualifiedReferrals->count() > 0)
        @include('profile.subscription.partials._coupon')
    @endif

    @if(webUser()->isEarlyAdapter())
        @include('profile.subscription.partials._earlyadapter', [
        '_plan' => \Kabooodle\Models\Plans::getEarlyAdapterPlan(),
        '_disable' => false
        ])
    @else
        @if(! webUser()->hasUserAlreadyHadGenericTrial())
        <div class="box warning">
            <div class="box-body">
                <div class="text-center center-block">
                    <h5><strong>Try the MERCHANT PLUS PLAN free for 30 days! </strong> </h5>
                    <button
                            type="button"
                            @click="subscribeToTrial"
                            class="btn white text-black-dk">
                    Start Your Trial Now!
                    </button>
                </div>
            </div>
        </div>
        @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-6" style="height: 810px">
                    @include('profile.subscription.partials._plan', [
                    '_plan' => \Kabooodle\Models\Plans::getMerchantPlanGroup(),
                    '_disable' => false
                    ])
                </div>
                <div class="col-sm-6" style="height: 810px">
                    @include('profile.subscription.partials._plan', [
                    '_plan' => \Kabooodle\Models\Plans::getMerchantPlusPlanGroup(),
                    '_disable' => false
                    ])
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection


@push('footer-scripts')
<script>
    const sub_endpoint = "{{ apiRoute('subscription.trial.store') }}";
</script>
<script src="{{ staticAsset('/assets/js/profile-subscription.js') }}"></script>
@endpush