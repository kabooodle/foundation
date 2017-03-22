<li><a href="{{ route('profile.index') }}" class="nav-link {{ Request::is('profile') ? 'active' : null }}">
    Profile Settings
</a></li>
<li><a href="{{ route('profile.emails.index') }}" class="nav-link {{ Request::is('profile/emails') ? 'active' : null }}">
    Email Addresses
</a></li>
<li><a href="{{ route('profile.notifications.edit') }}" class="nav-link {{ Request::is('profile/notifications') ? 'active' : null }}">
    Notification Settings
</a></li>
<li><a href="{{ route('profile.social.edit') }}" class="nav-link {{ Request::is('profile/socialprofiles') ? 'active' : null }}">
    Social Profiles
</a></li>
<li><a href="{{ route('profile.shippingprofile.edit') }}" class="nav-link {{ Request::is('profile/shippingprofile') ? 'active' : null }}">
    Shipping Profile
</a></li>
@if(webUser()->hasAtLeastMerchantSubscription() || (webUser()->getAvailableBalance() > 0))
<li><a href="{{ route('profile.credits.index') }}" class="nav-link {{ Request::is('profile/credits') ? 'active' : null }}">
    Credits
</a></li>
@endif
<li><a href="{{ route('profile.subscription.index') }}" class="nav-link {{ Request::is('profile/subscription') ? 'active' : null }}">
    Subscription
</a></li>
@if(webUser()->hasStripeId() || (webUser()->hasAtLeastMerchantSubscription() && ! webUser()->onGenericTrial()))
<li><a href="{{ route('profile.subscription.invoices.index') }}" class="nav-link {{ Request::is('profile/subscription/invoices') ? 'active' : null }}">
    Receipts
</a></li>
@endif
<li><a href="{{ route('profile.creditcard.index') }}" class="nav-link {{ Request::is('profile/creditcard') ? 'active' : null }}">
    Credit Card
</a></li>