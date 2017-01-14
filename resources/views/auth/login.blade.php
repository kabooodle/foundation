@extends('layouts.minimal')

@section('body-content')

    {{--@push('header-scripts')--}}
    {{--<script>--}}
        {{--Turbolinks.clearCache();--}}
    {{--</script>--}}
    {{--@endpush--}}


        <div class="text-center center-block">
            <a href="/" class="">
                <span class="inline"><img src="{{ staticAsset('/assets/images/logo/logo-prpl-md.png') }}"></span>
            </a>
        </div>


    <div class="p-a-md box-color r box-shadow-z1 text-color" id="sign-in-content">

        <sign-in
                sign-in-route="{{ route('auth.login.store') }}"
                password-reset-route="{{ route('auth.password.reset.index') }}"
                csrf="{{ csrf_token() }}"
                redirect="{{ $redirect or null }}"
        ></sign-in>

    </div>

    <div class="p-v-lg text-center">
        <div>Don't have an account? <a href="{{ route('auth.register') }}" class="text-primary _500">Create one!</a></div>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/sign-in.js') }}"></script>
@endpush
