<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" @yield('body-class', null)  {{ user() && user()->onGenericTrial() ? ' on-trial ' : null }} ">
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '513860408823933',
                cookie: true,
                xfbml: true,
                version: 'v2.7',
                scope: 'email,user_managed_groups,publish_actions,publish_pages'
            });
            $(document).trigger('fbload');
        };
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

        @push('utilities')
            <loader></loader>
            <popout-overlay></popout-overlay>
        @endpush


        <div id="kabooodle_utilities">
            @stack('utilities')
        </div>

        <div class="app" id="{{ $contentId or 'kabooodle_app' }}">
            <div id="content" class="app-content box-shadow-z0" role="main">
                @include('layouts.header._header')

                @if(user() && user()->onGenericTrial())
                    <div class="notificationbar b-b">
                        <p><span class="label">Notice</span> Trial ends {{ user()->genericTrialEndsInDays() }}. <a href="{{ route('profile.subscription.index') }}">Subscribe now. <i class="fa fa-angle-right" aria-hidden="true"></i></a></p>
                    </div>
                @endif

                @hasSection('body-menu')
                @include('layouts.partials._bodymenu')
                @endif

                <div ui-view class="app-body" id="view">
                    <div class="container">
                        <div class="p-t-2 p-b-3">
                            @yield('body-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer._footer')

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>