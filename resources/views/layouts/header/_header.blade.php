<div class="app-header navbar-md prpl-1000 box-shadow">
    <div class="navbar clearfix">
        <a class="navbar-item right hidden-md-up m-a-0 m-l" data-target="#primary_header_nav" data-animation="false" data-toggle="collapse"><i class=
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
                        <a class="nav-link text-sm hidden-md-down" href="{{ route('profile.credits.index') }}">{{ currency(webUser()->getAvailableBalance()) }} Credits</a>
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
                    {{--<a class="dropdown-item text-center" href="{{ route('notices.index') }}">View All Notices</a>--}}
                </div>
                </li>
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle clear" data-toggle=
                    "dropdown" href=""><span class="avatar_container _32 inline avatar-thumbnail"><img alt="..."
                                                                                                       src="{{ webUser()->avatar->location }}"> <i class=
                                                                                                                                      "busy b-white right"></i></span></a>
                    <div class="dropdown-menu pull-right">
                        <a class="dropdown-item" href="{{ route('user.profile', [webUser()->username]) }}"><span>Public Profile</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><span>Account Settings</span></a>
                        @if(webUser()->hasAtLeastMerchantSubscription() || (webUser()->getAvailableBalance() > 0))
                            <a class="dropdown-item hidden-lg-up" href="{{ route('profile.credits.index') }}"><span>{{ currency(webUser()->getAvailableBalance()) }} Credits</span></a>
                        @endif
                        <a class="dropdown-item {{ Request::is('referrals') ? 'active' : null }}" href="{{ route('referrals.index') }}"><span>Referrals</span></a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:;" type="button" class=
                           "js-toggle-help dropdown-item" >Help</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('auth.logout') }}" method="POST">
                        <a onclick="$(this).closest('form').submit()" class=
                           "dropdown-item" href="javascript:;">Sign out</a></form>
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

        <div class="collapse navbar-toggleable-sm" data-pjax="" data-animation="false" id="primary_header_nav">
            <ul class="nav navbar-nav pull-left nav-active-border b-warning">
                @if(webUser() && webUser()->hasAtLeastMerchantSubscription())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle "
                           href="#" data-toggle="dropdown"><span
                                    class="nav-text">Merchant Services</span></a>
                        <div class="dropdown-menu">
                            <a href="{{  route('shop.inventory.create', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">Add Inventory</a>
                            <a href="{{  route('shop.inventory.overview.show', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory') ? 'active' : null }}">Inventory</a>

                            <div class="divider"></div>
                            <a href="{{  route('shop.outfits.create', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/outfits/create') ? 'active' : null }}">Create Outfits</a>
                            <a href="{{  route('shop.outfits.index', [webUser()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/outfits') ? 'active' : null }}">Outfits</a>

                            <div class="divider"></div>
                            <a href="{{ route('merchant.listings.create') }}" class="dropdown-item {{ Request::is('merchant/listings/create') ? 'active' : null }}"><span>Create sales listing</span></a>
                            <a href="{{ route('merchant.listings.index') }}" class="dropdown-item {{ Request::is('merchant/listings') ? 'active' : null }}"><span>Manage Listings</span></a>

                            <div class="divider"></div>
                            <a href="{{ route('merchant.claims.index') }}"
                               class="dropdown-item {{ Request::is('merchant/claims') ? 'active' : null }}">Claims
                            </a>

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
                @if(webUser() && webUser()->hasAtLeastMerchantSubscription())
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

                            @if(webUser())
                                <div class="divider"></div>
                                <a href="{{ route('flashsales.index') }}"
                                   class="dropdown-item {{ Request::is('flashsales') ? 'active' : null }}">Manage Active Sales</a>
                            @endif

                        </div>
                    </li>
                @else
                    <li class ="nav-item ">
                        <a href="{{ route('flashsales.index') }}"
                           class="nav-link {{ Request::is('flashsales') ? 'active' : null }}"><span class="nav-text">Flash Sales</span></a>
                    </li>
                @endif
                @if(webUser())
                    <li class ="nav-item ">
                        <a href="{{ route('profile.claims.index') }}"
                           class="nav-link {{ Request::is('claims*') ? 'active' : null }}"><span class="nav-text">My Claims</span></a>
                    </li>
                    <li class ="nav-item ">
                        <a href="{{ route('watching.items.index', [webUser()->username]) }}"
                           class="nav-link {{ Request::is('watching*') ? 'active' : null }}"><span class="nav-text">Watching</span></a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<script>
//    function init() {
//        window.addEventListener('scroll', function(e){
//            var distanceY = window.pageYOffset || document.documentElement.scrollTop,
//                    shrinkOn = 80,
//                    header = $(".app-header");
//            if (distanceY > shrinkOn) {
//                $('body').addClass('header-condensed');
//            } else {
//                $('body').removeClass('header-condensed');
//            }
//        });
//    }
//    window.onload = init();
</script>