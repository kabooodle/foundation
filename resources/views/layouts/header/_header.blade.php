<div class="app-header navbar-md prpl-1000 box-shadow">
    <div class="navbar clearfix">
        <a class="navbar-item pull-right hidden-md-up m-a-0 m-l" data-target="#navbar-4" data-toggle="collapse"><i class=
                                              "material-icons"></i></a>
        <a href="{{ webUser() ? '/users/'.webUser()->username : '/home' }}"
                                                                           class="navbar-brand kabooodle-brand">
            <span class="svg-logo">
                @include('partials._logo_svg_lg')
            </span>
            <span class="svg-logo-sm">
                @include('partials._logo_svg_sm')
            </span>
        </a>

        <ul class="nav navbar-nav pull-right nav-active-border">
            @if(webUser())
                @if(webUser()->hasAtLeastMerchantSubscription() || (webUser()->getAvailableBalance() > 0))
                    <li class="nav-item ">
                        <a class="nav-link text-sm hidden-md-down" href="{{ route('profile.credits.index') }}">${{ webUser()->getAvailableBalance() }} Credits</a>
                    </li>
                @endif
                <li class="nav-item ">
                    <a class="nav-link"  href="{{ route('messenger.index') }}">
                        <i class="fa fa-comments" aria-hidden="true"></i><span class="label up indicator warning hide" ></span>
                    </a>
                </li>

                <li id="notices_wrapper" class="nav-item dropdown" @click="markUnreadAsRead('{{ apiRoute('notices.all.mark_as_read') }}')">
                <a class="nav-link" data-toggle="dropdown" href="">
                    <i class="fa fa-bell-o " aria-hidden="true"></i><span class="label up indicator warning hide" id="notify_total"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg pull-right ">
                    <div class="scrollable" data-scrollable="scrollable">
                        <ul class="p-a-0 p-0 m-0 m-a-0">
                            <notices
                                    limit="10"
                                    endpoint="{{ apiRoute('notices.index') }}"
                            ></notices>
                        </ul>
                    </div>
                    <a class="dropdown-item text-center" href="{{ route('notices.index') }}">View All Notices</a>
                </div>
                </li>
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle clear" data-toggle=
                    "dropdown" href=""><span class="avatar_container _32 inline avatar-thumbnail"><img alt="..."
                                                                                                       src="{{ webUser()->avatar->location }}"> <i class=
                                                                                                                                      "busy b-white right"></i></span></a>
                    <div class="dropdown-menu pull-right">
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><span>Account Settings</span></a>
                        @if(webUser()->hasAtLeastMerchantSubscription() || (webUser()->getAvailableBalance() > 0))
                            <a class="dropdown-item hidden-lg-up" href="{{ route('profile.credits.index') }}"><span>${{ webUser()->getAvailableBalance() }} Credits</span></a>
                        @endif
                        <a class="dropdown-item {{ Request::is('referrals') ? 'active' : null }}" href="{{ route('referrals.index') }}"><span>Referrals</span></a>
                        <a class=
                           "dropdown-item" href="{{ route('auth.logout') }}">Sign out</a>
                    </div>
                </li>
            @else
                <li class="nav-item m-l-2">
                    <a href="{{ route('auth.register') }}" class="_800 nav-link"><span class="nav-text">Register</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('auth.login') }}" class="_800 nav-link "><span class="nav-text">Sign in</span></a>
                </li>
            @endif
        </ul>

        <div class="collapse navbar-toggleable-sm" data-pjax="" id="navbar-4">
            <ul class="nav navbar-nav pull-left nav-active-border b-warning">
                @if(webUser() && webUser()->hasAtLeastMerchantSubscription())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle "
                           href="#" data-toggle="dropdown"><span
                                    class="nav-text">Merchant Services</span></a>
                        <div class="dropdown-menu">
                            <a href="{{  route('shop.inventory.create', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">Add Inventory</a>
                            <a href="{{  route('shop.inventory.index', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory') ? 'active' : null }}">Inventory</a>
                            <a href="{{  route('shop.outfits.index', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/outfits') ? 'active' : null }}">Outfits</a>
                            <div class="divider"></div>
                            <a href="{{ route('shop.claims.index', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/claims') ? 'active' : null }}">Pending Claims
                                <span class="">({{ webUser()->pendingClaimsOnMyInventory()->count() }})</span>
                            </a>

                            <a  href="{{  route('merchant.sales.index') }}"
                                class="dropdown-item {{ Request::is('merchant/sales*') ? 'active' : null }}"><span>Completed Sales</span></a>

                            <div class="divider"></div>
                            <a href="{{ route('merchant.listings.index') }}" class="dropdown-item {{ Request::is('merchant/listings') ? 'active' : null }}"><span>Manage Listings</span></a>

                            @if(webUser()->isSubscribedToMerchantPlus())
                                <div class="divider"></div>
                                <a href="{{  route('merchant.shipping.create') }}"
                                   class="dropdown-item {{ Request::is('merchant/shipping/create*') ? 'active' : null }}"><span>Build Shipment</span></a>
                                <a href="{{  route('merchant.shipping.index') }}"
                                   class="dropdown-item {{ Request::is('merchant/shipping') ? 'active' : null }}"><span>Shipments</span></a>
                            @endif
                        </div>
                    </li>
                @endif
                {{--@if(webUser() && (webUser()->claimsAsBuyer->count() > 0 || ! webUser()->hasAtLeastMerchantSubscription()))--}}
                    @if(webUser())
                    <li class ="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"> <span class="nav-text">Purchases</span></a>
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.purchases.index') }}"
                               class="dropdown-item {{ Request::is('purchases*') ? 'active' : null }}">Purchases &amp; Claims</a>
                            <a href="{{ route('watching.items.index', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('watching*') ? 'active' : null }}">Watching</a>
                        </div>
                    </li>
                    @endif
                {{--@endif--}}
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle nav-link"
                       data-toggle="dropdown"
                       href="{{ route('flashsales.index') }}"><span class="nav-text">Flash Sales</span></a>
                    <div class="dropdown-menu">
                        @if(webUser())
                            <a href="{{ route('flashsales.create') }}"
                               class="dropdown-item {{ Request::is('flashsales/create') ? 'active' : null }}">Create</a>
                        @endif
                        <a href="{{ route('flashsales.index') }}"
                           class="dropdown-item {{ Request::is('flashsales') ? 'active' : null }}">Browse</a>

                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    function init() {
        window.addEventListener('scroll', function(e){
            var distanceY = window.pageYOffset || document.documentElement.scrollTop,
                    shrinkOn = 80,
                    header = $(".app-header");
            if (distanceY > shrinkOn) {
                $('body').addClass('header-condensed');
            } else {
                $('body').removeClass('header-condensed');
            }
        });
    }
    window.onload = init();
</script>