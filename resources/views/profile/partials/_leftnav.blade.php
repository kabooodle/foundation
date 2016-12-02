<a href="{{ route('profile.index') }}" class="nav-link {{ Request::is('profile') ? 'active' : null }}">
    Account Settings
</a>
<a href="{{ route('profile.notifications.edit') }}" class="nav-link {{ Request::is('profile/notifications') ? 'active' : null }}">
    Notifications
</a>
<a href="{{ route('profile.social.edit') }}" class="nav-link {{ Request::is('profile/socialprofiles') ? 'active' : null }}">
    Social Profiles
</a>
<a href="{{ route('profile.shippingprofile.edit') }}" class="nav-link {{ Request::is('profile/shippingprofile') ? 'active' : null }}">
    Shipping Profile
</a>
@if(user()->subscribed('main') || (user()->getAvailableBalance() > 0))
<a href="{{ route('profile.credits.index') }}" class="nav-link {{ Request::is('profile/credits') ? 'active' : null }}">
    Credits
</a>
@endif
<a href="{{ route('profile.subscription.index') }}" class="nav-link {{ Request::is('profile/subscription') ? 'active' : null }}">
    Subscription
</a>
@if(user()->subscribed('main'))
<a href="{{ route('profile.subscription.invoices.index') }}" class="nav-link {{ Request::is('profile/subscription/invoices') ? 'active' : null }}">
    Purchases
</a>
@endif
<a href="{{ route('profile.creditcard.index') }}" class="nav-link {{ Request::is('profile/creditcard') ? 'active' : null }}">
    Credit Card
</a>