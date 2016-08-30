<!DOCTYPE html>
    <html lang="en" class="bg">
    @include('layouts.header._htmlheader')
    <style>
        .bg {
            background: url('/assets/images/bg.jpg') no-repeat 50% 50% fixed;
            background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            -webkit-background-size: cover;
            background-color: inherit;
        }
    </style>
    <body class=" @yield('body-class', 'container') bg" id="kabooodle_app">

        <div class="app" style="background: url() center no-repeat;">
            <div id="content" class="center-block w-xxl w-auto-xs p-y-md" role="main">
                @yield('body-content')
            </div>
        </div>

        @include('layouts.footer._htmlfooter')

        @include('widgets.messages')

    </body>
</html>