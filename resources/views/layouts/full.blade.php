<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" @yield('body-class', 'a') " id="kabooodle_app">
        <div class="app" id="app">
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

        @include('layouts.footer._footer')

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>