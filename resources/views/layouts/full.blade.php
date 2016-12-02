<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" @yield('body-class', null)">
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7&appId=680971485365927";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

        <div class="app" id="{{ $contentId or 'kabooodle_app' }}">
            <div id="content" class="app-content box-shadow-z0" role="main">
                @include('layouts.header._header')

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

        @push('utilities')
            <loader></loader>
            <popout-overlay></popout-overlay>
        @endpush


        <div id="kabooodle_utilities">
            @stack('utilities')
        </div>

        @include('layouts.footer._footer')

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>