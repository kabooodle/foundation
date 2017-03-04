<!DOCTYPE html>
    <html lang="en">

    @push('header-styles')
    <link rel="stylesheet" href="{{ staticAsset('/assets/css/home.css') }}" type="text/css"/>
    <link href="https://fonts.googleapis.com/css?family=Kreon:400,700" rel="stylesheet">
    @endpush

    @include('layouts.header._htmlheader')

    <body class=" @yield('body-class', null) home-content ">
        <div class="app" >

            <div id="home-container">
                @yield('content')
            </div>

            @include('layouts.footer._htmlfooter')

            @include('widgets.messages')
        </div>
    </body>
</html>