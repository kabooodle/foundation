<a href="{{ route('profile.index') }}" class="nav-link {{ Request::is('profile') ? 'active' : null }}">
    Profile
</a>
<a href="{{ route('profile.social.edit') }}" class="nav-link {{ Request::is('profile/notifications') ? 'active' : null }}">
    Notifications
</a>
<a href="{{ route('profile.social.edit') }}" class="nav-link {{ Request::is('profile/socialprofiles') ? 'active' : null }}">
    Social Profiles
</a>
<a href="{{ route('profile.addresses.edit') }}" class="nav-link {{ Request::is('profile/addresses') ? 'active' : null }}">
    Addresses
</a>
<a href="{{ route('profile.subscription.index') }}" class="nav-link {{ Request::is('profile/subscription') ? 'active' : null }}">
    Subscription
</a>
<a href="{{ route('profile.subscription.invoices.index') }}" class="nav-link {{ Request::is('profile/subscription/invoices') ? 'active' : null }}">
    Payments
</a>
<a href="{{ route('profile.creditcard.index') }}" class="nav-link {{ Request::is('profile/creditcard') ? 'active' : null }}">
    Credit Card
</a>