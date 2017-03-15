<p>Welcome to {{ env('APP_NAME') }}, {{ $user->first_name }}!! We look forward to saving you time and money when it comes to managing your direct sales business.<p>

<p>In order to take advantage of all {{ env('APP_NAME') }}'s great features, Please confirm your email:</p>
<a class="btn primary btn-block p-x-md" href="{{ route('emails.verify', [$user->primaryEmail->token]) }}">Verify your email</a>
<p>Or click on this link: <a href="{{ route('emails.verify', [$user->primaryEmail->token]) }}">{{ route('emails.verify', [$user->primaryEmail->token]) }}</a></p>

<p>Below you will find your referral link, this will give you a free month for each person you refer to {{ env('APP_NAME') }}, up to 6 months!</p>

--
the {{ env('APP_NAME') }} Team