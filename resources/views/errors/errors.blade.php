<!DOCTYPE html>
<html lang="en" class="bg">
@include('layouts.header._htmlheader')
<style>
    .bg {
        background: url('/assets/images/errors-bg.png') no-repeat top center fixed;
        background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        -webkit-background-size: cover;
        background-color: inherit;
    }
    .logo {
        width: 129px;
        height: 31px;
        position: fixed;
        top: 10px;
        margin: 0 auto 0 -64px;
        left: 50%;
    }
    h1 {
        color: #7e79b6;
        font-size: 400px;
        line-height: 1;
        text-align: center;
        margin: 0;
    }
    h6 {
        color: #7e79b6;
        font-size: 30px;
        text-align: center;
        margin: 0;
    }
    .center-all {
        position: absolute;
        margin: -240px auto 0;
        top: 50%;
        width: 100%;
    }
    @media (max-width: 960px) {
        h1 {
            font-size: 180px;
        }
        h6 {
            font-size: 16px;
        }
    }
</style>
<body class=" bg" id="kabooodle_app">
    <a href="/"><img src="/assets/images/logo/logo-white-xs.png" class="logo"></a>
<div class="center-all">
    @yield('content')
</div>
</body>
</html>