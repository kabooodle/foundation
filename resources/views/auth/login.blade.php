@extends('layouts.minimal')

@section('body-content')

    @push('header-scripts')
    <script>
        Turbolinks.clearCache();
    </script>
    @endpush


        <div class="text-center center-block">
            <a href="/" class="">
                <span class="inline"><img src="/assets/images/logo/logo-prpl-md.png"></span>
            </a>
        </div>


    <div class="p-a-md box-color r box-shadow-z1 text-color">

        {{ Form::open(['route' => 'auth.login.store']) }}

        <div class="md-form-group">
            {{ Form::text('email', null, ['class' => 'md-input']) }}
            <label>Email Address</label>
        </div>

        <div class="md-form-group">
            {{ Form::password('password', ['class' => 'md-input']) }}
            <label>Password <a href="{{ route('auth.password.reset.index') }}" class="text-accent text-primary _500 m-l-lg font-italic">Forgot password?</a></label>
        </div>

        <button type="submit" v-on:click="disableOnClick" class="btn primary btn-block p-x-md">Login</button>

        {{ Form::close() }}
    </div>

    <div class="p-v-lg text-center">
        <div>Don't have an account? <a href="{{ route('auth.register') }}" class="text-primary _500">Create one!</a></div>
    </div>



@endsection

