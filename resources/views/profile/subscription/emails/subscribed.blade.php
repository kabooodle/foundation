Subscription was activated successfully!

@if($user->subscription('main')->onTrial())
    Trial ends on: {{ $subscription->trial_ends_at }}
@endif

--
the {{ env('APP_NAME') }} Team