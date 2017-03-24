<p>Welcome to {{ env('APP_NAME') }}, {{ $user->first_name }}!!
@if($user->hasAtLeastMerchantSubscription())
    We look forward to saving you time and money when it comes to managing your direct sales business.
@endif
<p>

<p>In order to take advantage of all {{ env('APP_NAME') }}'s great features, Please confirm your email:</p>
<a class="btn primary btn-block p-x-md" href="{{ route('emails.verify', [$user->primaryEmail->token]) }}">Verify your email</a>
<p>Or click on this link: <a href="{{ route('emails.verify', [$user->primaryEmail->token]) }}">{{ route('emails.verify', [$user->primaryEmail->token]) }}</a></p>

<p><strong>Don't forget!</strong> In order to speed up the claiming process, please fill out a shipping address asap! This will help minimize the need for unnecessary back and forth between buyers and sellers, expediting the process!
    You can add update your address at anytime, as well as add multiple addresses too.  You can find your shipping profile in your account settings or by clicking here: <a href="{{ route('profile.shippingprofile.edit') }}">{{ route('profile.shippingprofile.edit') }}</a>
</p>

<p>Below you will find your referral link, this will give you a free month for each person you refer to {{ env('APP_NAME') }}, up to 6 months!</p>
<p><a href="{{ route('invite.index', [$user->username]) }}">{{ route('invite.index', [$user->username]) }}</a></p>

--
the {{ env('APP_NAME') }} Team