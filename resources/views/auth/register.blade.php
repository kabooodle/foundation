@extends('layouts.minimal')

@section('body-content')

    <div class="text-center center-block">
        <a href="/" class="">
            <span class="inline"><img src="{{ staticAsset('/assets/images/logo/logo-prpl-md.png') }}"></span>
        </a>
    </div>


    <div class="p-a-md box-color r box-shadow-z1 text-color" id="register-content">
        <register
                route="{{ route('auth.register.store') }}"
                csrf="{{ csrf_token() }}"
                redirect="{{ $redirect or null }}"
        ></register>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/register.js') }}"></script>
@endpush
