@extends('layouts.minimal')

@section('body-content')

    @include('auth.partials.logo')

    <div class="p-a-md box-color r box-shadow-z1 text-color" id="register-content">
        <register
            referrer="{{ isset($referrer) ? $referrer : null }}"
            route="{{ route('auth.register.store') }}"
            csrf="{{ csrf_token() }}"
            redirect="{{ $redirect or null }}"
        ></register>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/register.js') }}"></script>
@endpush
