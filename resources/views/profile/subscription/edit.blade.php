
@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Cancel Subscription</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p class="">If you cancel your subscription in the middle of a billing cycle, you'll still have access to the features until the cycle ends.</p>
            <p class="m-b-0">Your next scheduled billing date is: {{ webUser()->upcomingInvoice()->date()->format('F jS Y') }}, for {{ webUser()->upcomingInvoice()->total() }}.</p>
        </div>
        <form method="POST" action="{{ route('profile.subscription.destroy') }}">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
        <div class="box-body clearfix">
            <div class="pull-right">
                <button class="btn danger">Yes, cancel the renewal of my subscription immediately.</button>
            </div>
        </div>
        </form>
    </div>

@endsection