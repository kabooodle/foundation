
@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Subscribe to Plan</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            @if(webUser()->pendingQualifiedReferrals->count() > 0)
                @include('profile.subscription.partials._coupon')
            @endif
            @include('profile.subscription.partials._plan', [
                '_price' => $plan['price'],
                '_plan' => $planGroup,
                '_hideButtons' => true
            ])
        </div>
        <form method="POST" action="{{ route('profile.subscription.store', [$plan['id']]) }}">
            {{ csrf_field() }}
        <div class="box-body clearfix">
            @if(!$card)
                <input type="hidden" name="newcard" value="1">
                @include('profile.creditcard.partials._form')
            @endif
            <div class="pull-right">
                <button class="btn primary">Purchase - ${{ $plan['price'] }}/{{ $plan['interval'] }}</button>
                <a href="{{ route('profile.subscription.index') }}" class="btn white">Cancel</a>
            </div>
        </div>
        </form>
    </div>

@endsection