<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" header-condensed @yield('body-class', null)  {{ webUser() && webUser()->onGenericTrial() ? ' on-trial ' : null }} @hasSection('body-menu') @if(trim($__env->yieldContent('body-menu')) <> '') with-body-menu @else no-body-menu @endif @else  no-body-menu  @endif ">
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{ env('FACEBOOK_APP_ID') }}',
                cookie: true,
                xfbml: true,
                version: 'v2.8',
                scope: '{{ implode(config('laravel-facebook-sdk.default_scope')) }}'
            });
            $(document).trigger('fbload');
        };
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

        <div id="kabooodle_utilities">
            @stack('utilities')
        </div>

        <div class="app" id="kabooodle_app">
            <div id="content" class="app-content box-shadow-z0" role="main">
                @include('layouts.header._header')

                <div id="{{ $contentId or 'kabooodle_app_inner' }}" class="kabooodle_app_inner">
                    @if(webUser() && webUser()->onGenericTrial())
                        <div class="notificationbar b-b">
                            <p><span class="label">Notice</span> Trial ends {{ webUser()->genericTrialEndsInDays() }}. <a href="{{ route('profile.subscription.index') }}"> <strong>Subscribe now. <i class="fa fa-angle-right" aria-hidden="true"></i></strong></a></p>
                        </div>
                    @endif

                    @hasSection('body-menu')
                        @include('layouts.partials._bodymenu')
                    @endif

                    <div ui-view class="app-body" id="view">
                        <div class="container">
                            <div class="p-t-2 p-b-2 p-l-1 p-r-1">
                                @yield('body-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.footer._footer')
        </div>

@include('layouts.footer._htmlfooter')

@include('widgets.messages')

</body>
</html>