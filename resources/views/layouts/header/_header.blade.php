<div class="app-header navbar-md prpl-1000 box-shadow">
    <div class="navbar">

        <a class="navbar-item pull-right hidden-md-up m-a-0 m-l" data-target=
        "#navbar-4" data-toggle="collapse"><i class=
                                              "material-icons"></i></a><a href="/"
                                                                           class="navbar-brand kabooodle-brand">
<span class=
      "hidden-folded inline">{{ env('APP_NAME') }}</span></a>

        <ul class="nav navbar-nav pull-right nav-active-border">
            @if(user())
                @if(user()->subscribed('main') || (user()->getAvailableBalance() > 0))
                <li class="nav-item ">
                    <a class="nav-link text-sm" href="{{ route('profile.credits.index') }}">${{ user()->getAvailableBalance() }} Credits</a>
                </li>
                @endif
                {{--<li class="nav-item dropdown">--}}
                    {{--<a class="nav-link" data-toggle="dropdown" href=""><i class=--}}
                                                                          {{--"material-icons"></i> <span class=--}}
                                                                                                       {{--"label up warning hide"--}}
                                                                                                       {{--id="notify_total"></span></a>--}}
                    {{--<div class=--}}
                         {{--"dropdown-menu pull-right w-xl no-bg no-border no-shadow">--}}
                        {{--<div class="scrollable" style="max-height: 220px">--}}
                            {{--<ul class="list-group m-a-0">--}}
                                {{--<li class=--}}
                                    {{--"list-group-item black lt box-shadow-z0 b">--}}
                            {{--<span class="pull-left m-r"><img alt="..." class=--}}
                                {{--"w-40 img-circle" src=--}}
                                                             {{--"../assets/images/a0.jpg"></span> <span class=--}}
                                                                                                     {{--"clear block">Use awesome <a--}}
                                                {{--class="text-primary"--}}
                                                {{--href="">animate.css</a><br>--}}
                            {{--<small class="text-muted">10 minutes--}}
                            {{--ago</small></span></li>--}}
                                {{--<li class=--}}
                                    {{--"list-group-item black lt box-shadow-z0 b">--}}
                            {{--<span class="pull-left m-r"><img alt="..." class=--}}
                                {{--"w-40 img-circle" src=--}}
                                                             {{--"../assets/images/a1.jpg"></span> <span class=--}}
                                                                                                     {{--"clear block"><a--}}
                                                {{--class="text-primary" href=--}}
                                        {{--"">Joe</a> Added you as friend<br>--}}
                            {{--<small class="text-muted">2 hours--}}
                            {{--ago</small></span></li>--}}
                                {{--<li class=--}}
                                    {{--"list-group-item dark-white text-color box-shadow-z0 b">--}}
                            {{--<span class="pull-left m-r"><img alt="..." class=--}}
                                {{--"w-40 img-circle" src=--}}
                                                             {{--"https://placekitten.com/g/32/32"></span> <span class=--}}
                                                                                                             {{--"clear block"><a--}}
                                                {{--class="text-primary" href=--}}
                                        {{--"">Danie</a> sent you a message<br>--}}
                            {{--<small class="text-muted">1 day--}}
                            {{--ago</small></span></li>--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</li>--}}
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle clear" data-toggle=
                    "dropdown" href=""><span class="avatar_container _32 inline avatar-thumbnail"><img alt="..."
                                                                      src="{{ user()->avatar }}"> <i class=
                                                                                                                "busy b-white right"></i></span></a>
                    <div class="dropdown-menu pull-right">
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><span>Account Settings</span></a>

                        <a class="dropdown-item" href="{{ route('referrals.index') }}"><span>Referrals</span></a>
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
                @if(user() && user()->subscribed('main'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle "
                           href="#" data-toggle="dropdown"><span
                                    class="nav-text">Merchant Services</span></a>
                        <div class="dropdown-menu">
                            <a href="{{  route('shop.inventory.create', [user()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">Add Inventory</a>
                            <a href="{{  route('shop.inventory.index', [user()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/inventory') ? 'active' : null }}">Inventory</a>
                            <div class="divider"></div>
                            <a href="{{ route('shop.claims.index', [user()->username]) }}"
                               class="dropdown-item {{ Request::is('shop/*/claims') ? 'active' : null }}">Pending Claims</a>

                            <a  href="{{  route('sales.index') }}"
                                class="dropdown-item {{ Request::is('sales*') ? 'active' : null }}"><span>Completed Sales</span></a>

                            <div class="divider"></div>
                            <a href="{{ route('listings.index') }}" class="dropdown-item {{ Request::is('listings') ? 'active' : null }}"><span>Listings</span></a>

                            <div class="divider"></div>
                            <a href="{{  route('shipping.create') }}"
                               class="dropdown-item {{ Request::is('shipping/create*') ? 'active' : null }}"><span>Build Shipment</span></a>
                            <a href="{{  route('shipping.index') }}"
                               class="dropdown-item {{ Request::is('shipping') ? 'active' : null }}"><span>Shipments</span></a>
                        </div>
                    </li>
                @endif
                    <li class="nav-item">
                        <a href="{#"
                           class="nav-link {{ Request::is('purchases*') ? 'active' : null }}"><span
                                    class="nav-text">Purchases</span></a>
                    </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('groups*') ? 'active' : null }}"
                       href="{{ route('groups.index') }}" ui-sref-active="active"><span class=
                                                                                        "nav-text">Groups</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('flashsales*') ? 'active' : null }}"
                       href="{{ route('flashsales.index') }}"><span class="nav-text">Flash Sales</span></a>
                </li>
            </ul>

            <div class="navbar-form form-inline pull-right pull-none-sm navbar-item v-m">
                {{--<div class="form-group l-h m-a-0">--}}
                    {{--<input type="text" id="app_search" class="nav-search-input half-rounded form-control b-a" placeholder="Search {{ appName() }}...">--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
</div>