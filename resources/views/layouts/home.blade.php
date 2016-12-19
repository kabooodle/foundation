<!DOCTYPE html>
    <html lang="en">
    @include('layouts.header._htmlheader')
    <body class=" @yield('body-class', null)  ">

        <div class="app" id="{{ $contentId or 'kabooodle_app' }}">
            <div id="content" class="app-content box-shadow-z0" role="main">
                @include('layouts.header._header')
                @yield('content')
            </div>
        </div>

        @include('layouts.footer._footer')

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>