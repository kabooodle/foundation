<p>{{ $referee->full_name }}, thank you for referring {!! $user->full_name !!} ({{ $user->username }}) to {{env('APP_NAME')}} today!</p>
<p>To view all your referrals, credits, and prizes, visit your referrals page here: <a href="{{ route('referrals.index') }}">{{ route('referrals.index') }}</a></p>
<p>Thank you!</p>

--
the {{ env('APP_NAME') }} Team
