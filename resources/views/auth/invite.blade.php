@extends('layouts.minimal')

@section('body-content')

    <div class="text-center center-block">
        <a href="/" class="">
            <span class="inline"><img src="/assets/images/logo/logo-prpl-md.png"></span>
        </a>
    </div>


    <div class="box">
        <div class="box-header">
            <h2>{{ $user->name }} has invited you to join Kabooodle!</h2>
        </div>
        <div class="box-divider m-a-0"></div>
            <div class="box-body">

                {{ Form::open(['method' => route('auth.register.store')]) }}

                <div class="md-form-group">
                    {{ Form::text('name', null, ['class' => 'md-input']) }}
                    <label>Name</label>
                </div>

                <div class="md-form-group">
                    {{ Form::text('email', null, ['class' => 'md-input']) }}
                    <label>Email Address</label>
                </div>

                <div class="md-form-group">
                    {{ Form::password('password', ['class' => 'md-input']) }}
                    <label>Password</label>
                </div>

                <p class="">By clicking on "Create Account" below, you are agreeing to the <a href="" class="text-info">Terms of Service</a> and the <a href="" class="text-info">Privacy Policy</a>.</p>

                <button type="submit" v-on:click="disableOnClick" class="btn primary btn-block p-x-md">Create Account</button>

                {{ Form::close() }}
        </div>
    </div>

@endsection

