<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" @yield('body-class', 'container') no-shadow" id="kabooodle_app">

        <div class="app no-shadow" style="padding-top: 40px;">
            <div id="content" class="center-block w-420 w-auto-xs no-shadow p-y-md" role="main">
                @yield('body-content')
            </div>
        </div>

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>