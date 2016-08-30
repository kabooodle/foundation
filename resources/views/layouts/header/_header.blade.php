<div class="app-header navbar-md prpl-1000 box-shadow">
    <div class="navbar">

        <a class="navbar-item pull-right hidden-md-up m-a-0 m-l" data-target=
        "#navbar-4" data-toggle="collapse"><i class=
                                              "material-icons"></i></a><a href="/" class="navbar-brand kabooodle-brand">
<span class=
                                                                         "hidden-folded inline">{{ env('APP_NAME') }}</span></a>

        <ul class="nav navbar-nav pull-right nav-active-border">
            @if(user())
            {{--<li class="nav-item dropdown dropdown-onhover">--}}
                {{--<a class="nav-link" data-toggle="dropdown" href=""><i class=--}}
                                                                      {{--"material-icons"></i> <span class=--}}
                                                                                                   {{--"label up p-a-0 accent"></span></a>--}}
                {{--<div class=--}}
                     {{--"dropdown-menu w-xl text-color pull-right p-a-0">--}}
                    {{--<div class="row no-gutter text-warning-hover">--}}
                        {{--<div class="col-xs-4 b-r b-b">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">Alarm</span></a>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4 b-r b-b">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">Favorite</span></a>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4 b-b">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">History</span></a>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4 b-r">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">Call</span></a>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4 b-r">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">Chat</span></a>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4">--}}
                            {{--<a class="p-a block text-center"><i class=--}}
                                                                {{--"material-icons md-24 text-muted m-v-sm"></i>--}}
                                {{--<span class="block">Inbox</span></a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</li>--}}
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href=""><i class=
                                                                      "material-icons"></i> <span class=
                                                                                                   "label up warning hide" id="notify_total"></span></a>
                <div class=
                     "dropdown-menu pull-right w-xl no-bg no-border no-shadow">
                    <div class="scrollable" style="max-height: 220px">
                        <ul class="list-group m-a-0">
                            <li class=
                                "list-group-item black lt box-shadow-z0 b">
                            <span class="pull-left m-r"><img alt="..." class=
                                "w-40 img-circle" src=
                                                             "../assets/images/a0.jpg"></span> <span class=
                                                                                                     "clear block">Use awesome <a
                                            class="text-primary"
                                            href="">animate.css</a><br>
                            <small class="text-muted">10 minutes
                            ago</small></span></li>
                            <li class=
                                "list-group-item black lt box-shadow-z0 b">
                            <span class="pull-left m-r"><img alt="..." class=
                                "w-40 img-circle" src=
                                                             "../assets/images/a1.jpg"></span> <span class=
                                                                                                     "clear block"><a
                                            class="text-primary" href=
                                    "">Joe</a> Added you as friend<br>
                            <small class="text-muted">2 hours
                            ago</small></span></li>
                            <li class=
                                "list-group-item dark-white text-color box-shadow-z0 b">
                            <span class="pull-left m-r"><img alt="..." class=
                                "w-40 img-circle" src=
                                                             "https://placekitten.com/g/32/32"></span> <span class=
                                                                                                            "clear block"><a
                                            class="text-primary" href=
                                    "">Danie</a> sent you a message<br>
                            <small class="text-muted">1 day
                            ago</small></span></li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-onhover">
                <a class="nav-link dropdown-toggle clear" data-toggle=
                "dropdown" href=""><span class="avatar w-32"><img alt="..."
                                                                  src="https://placekitten.com/g/32/32"> <i class=
                                                                                                           "busy b-white right"></i></span></a>
                <div class="dropdown-menu pull-right dropdown-menu-scale">
                    <a class="dropdown-item" ui-sref=
                    "app.inbox.list"><span>Inbox</span> <span class=
                                                              "label warn m-l-xs">3</span></a> <a class="dropdown-item"
                                                                                                  ui-sref="app.page.profile"><span>Profile</span></a>
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
            {{--<form class=--}}
                  {{--"navbar-form form-inline pull-right pull-none-sm navbar-item v-m"--}}
                  {{--role="search">--}}
                {{--<div class="form-group l-h m-a-0">--}}
                    {{--<div class="input-group input-group-sm">--}}
                        {{--<input class="form-control p-x b-a rounded"--}}
                               {{--placeholder="Search projects..." type="text">--}}
                        {{--<span class="input-group-btn"><button class=--}}
                                                              {{--"btn white b-a rounded no-shadow" type=--}}
                                                              {{--"submit"><span class="input-group-btn"><i class=--}}
                                                                                                        {{--"fa fa-search"></i></span></button></span>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</form>--}}
            <ul class="nav navbar-nav pull-left nav-active-border b-warning">
                @if(user())
                <li class="nav-item">
                    <a href="{{  route('shop.show', [user()->username]) }}" class="nav-link {{ Request::is('shop/'.user()->username.'') ? 'active' : null }}"><span class="nav-text">Your Store</span></a>
                </li>
                <li class="nav-item dropdown dropdown-onhover">
                    <a class="nav-link dropdown-toggle {{ Request::is('shop/*/inventory*') ? 'active' : null }}" href="{{ route('shop.inventory.index', [user()->username]) }}" data-toggle="dropdown"><span class="nav-text">Inventory</span></a>
                    <div class="dropdown-menu">
                        <a href="{{  route('shop.inventory.index', [user()->username]) }}" class="dropdown-item {{ Request::is('shop/*/inventory') ? 'active' : null }}">Manage Items</a>
                        <a href="{{ route('shop.inventory.create', [user()->username]) }}" class="dropdown-item {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">Add Items</a>
                    </div>
                    {{--<div class=--}}
                         {{--"dropdown-menu pull-down p-a w-full text-color text-primary-hover">--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-4">--}}
                                {{--<div class="_600 text-u-c m-y-sm">--}}
                                    {{--UI kits <span class=--}}
                                                  {{--"label label-sm success">10</span>--}}
                                {{--</div>--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-xs-6">--}}
                                        {{--<ul class="nav l-h-2x">--}}
                                            {{--<li class="hide" ng-class=--}}
                                            {{--"{'show': 1}">--}}
                                                {{--<a ui-sref=--}}
                                                   {{--"app.ui.angularstrap"><span>AngularStrap</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="arrow.html" ui-sref=--}}
                                                {{--"app.ui.arrow"><span>Arrow</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="box.html" ui-sref=--}}
                                                {{--"app.ui.box"><span>Box</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="button.html" ui-sref=--}}
                                                {{--"app.ui.button"><span>Button</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="color.html" ui-sref=--}}
                                                {{--"app.ui.color"><span>Color</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="dropdown.html"--}}
                                                   {{--ui-sref="app.ui.dropdown"><span>--}}
                                                {{--Dropdown</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="grid.html" ui-sref=--}}
                                                {{--"app.ui.grid"><span>Grid</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="icon.html" ui-sref=--}}
                                                {{--"app.ui.icon"><span>Icon</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="label.html" ui-sref=--}}
                                                {{--"app.ui.label"><span>Label</span></a>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xs-6">--}}
                                        {{--<ul class="nav l-h-2x">--}}
                                            {{--<li>--}}
                                                {{--<a href="list.html" ui-sref=--}}
                                                {{--"app.ui.list"><span>List--}}
                                                {{--Group</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="modal.html" ui-sref=--}}
                                                {{--"app.ui.modal"><span>Modal</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="nav.html" ui-sref=--}}
                                                {{--"app.ui.nav"><span>Nav</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="progress.html"--}}
                                                   {{--ui-sref="app.ui.progress"><span>--}}
                                                {{--Progress</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="social.html" ui-sref=--}}
                                                {{--"app.ui.social"><span>Social</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="streamline.html"--}}
                                                   {{--ui-sref=--}}
                                                   {{--"app.ui.streamline"><span>Streamline</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="timeline.html"--}}
                                                   {{--ui-sref="app.ui.timeline"><span>--}}
                                                {{--Timeline</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="vectormap.html"--}}
                                                   {{--ui-sref=--}}
                                                   {{--"app.ui.vectormap"><span>Vector--}}
                                                {{--Map</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li class="hide" ng-class=--}}
                                            {{--"{'show': 1}">--}}
                                                {{--<a ui-sref=--}}
                                                   {{--"app.googlemap"><span>Google--}}
                                                {{--Map</span></a>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-4">--}}
                                {{--<div class="_600 text-u-c m-y-sm">--}}
                                    {{--Pages <span class=--}}
                                                {{--"label label-sm">12</span>--}}
                                {{--</div>--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-xs-6">--}}
                                        {{--<ul class="nav l-h-2x">--}}
                                            {{--<li>--}}
                                                {{--<a href="profile.html" ui-sref=--}}
                                                {{--"app.page.profile"><span>Profile</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="setting.html" ui-sref=--}}
                                                {{--"app.page.settings"><span>Settings</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="search.html" ui-sref=--}}
                                                {{--"app.page.search"><span>Search</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="faq.html" ui-sref=--}}
                                                {{--"app.page.faq"><span>FAQ</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="invoice.html" ui-sref=--}}
                                                {{--"app.page.invoice"><span>Invoice</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="price.html" ui-sref=--}}
                                                {{--"app.page.price"><span>Price</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="blank.html" ui-sref=--}}
                                                {{--"app.page.blank"><span>Blank</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="signin.html" ui-sref=--}}
                                                {{--"access.signin"><span>Sign--}}
                                                {{--In</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="signup.html" ui-sref=--}}
                                                {{--"access.signup"><span>Sign--}}
                                                {{--Up</span></a>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xs-6">--}}
                                        {{--<ul class="nav l-h-2x">--}}
                                            {{--<li>--}}
                                                {{--<a href="forgot-password.html"--}}
                                                   {{--ui-sref=--}}
                                                   {{--"access.forgot-password"><span>Forgot--}}
                                                {{--Password</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="lockme.html" ui-sref=--}}
                                                {{--"access.lockme"><span>Lockme--}}
                                                {{--Screen</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="404.html" ui-sref=--}}
                                                {{--"404"><span>Error--}}
                                                {{--404</span></a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="505.html" ui-sref=--}}
                                                {{--"505"><span>Error--}}
                                                {{--505</span></a>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<div class="_600 text-u-c m-y-sm">--}}
                                    {{--Form--}}
                                {{--</div>--}}
                                {{--<ul class="nav l-h-2x">--}}
                                    {{--<li>--}}
                                        {{--<a href="form.layout.html" ui-sref=--}}
                                        {{--"app.form.layout"><span>Form--}}
                                        {{--Layout</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="form.element.html" ui-sref=--}}
                                        {{--"app.form.element"><span>Form--}}
                                        {{--Element</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.validation"><span>Form--}}
                                        {{--Validation</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.select"><span>Select</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.editor"><span>Editor</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.slider"><span>Slider</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.tree"><span>Tree</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.file-upload"><span>File--}}
                                        {{--Upload</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.image-crop"><span>Image--}}
                                        {{--Crop</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.form.editable"><span>Editable</span></a>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<div class="_600 text-u-c m-y-sm">--}}
                                    {{--Table--}}
                                {{--</div>--}}
                                {{--<ul class="nav l-h-2x">--}}
                                    {{--<li>--}}
                                        {{--<a href="static.html" ui-sref=--}}
                                        {{--"app.table.static"><span>Static--}}
                                        {{--table</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="datatable.html" ui-sref=--}}
                                        {{--"app.table.datatable"><span>Datatable</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="footable.html" ui-sref=--}}
                                        {{--"app.table.footable"><span>Footable</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.table.smart"><span>Smart--}}
                                        {{--table</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref="app.table.nggrid"><span>NG--}}
                                        {{--Grid</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref="app.table.uigrid"><span>UI--}}
                                        {{--Grid</span></a>--}}
                                    {{--</li>--}}
                                    {{--<li class="hide" ng-class="{'show': 1}">--}}
                                        {{--<a ui-sref=--}}
                                           {{--"app.table.editable"><span>Editable</span></a>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('groups*') ? 'active' : null }}" href="{{ route('groups.index') }}" ui-sref-active="active"><span class=
                                                                               "nav-text">Groups</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('flashsales*') ? 'active' : null }}"
                       href="{{ route('flashsales.index') }}"><span class="nav-text">Flash Sales</span></a>
                </li>
            </ul>

            <div class="navbar-form form-inline pull-right pull-none-sm navbar-item v-m"><div class="form-group l-h m-a-0"><input type="text" id="app_search" class="nav-search-input half-rounded form-control b-a" placeholder="Search {{ appName() }}..."></div></div>
        </div>
    </div>
</div>