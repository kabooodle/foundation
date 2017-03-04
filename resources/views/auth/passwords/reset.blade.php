@extends('layouts.minimal')

@section('body-content')

    @include('auth.partials.logo')


    <div class="p-a-md box-color r box-shadow-z1 text-color">

        {{ Form::open(['route' => ('auth.password.reset.reset')]) }}

        {{ Form::hidden('token', $token) }}

        <div class="md-form-group">
            {{ Form::text('email', ($email ? : old('email')), ['class' => 'md-input']) }}
            <label>Email Address</label>
        </div>

        <div class="md-form-group">
            {{ Form::password('password', ['class' => 'md-input']) }}
            <label>Password</label>
        </div>

        <div class="md-form-group">
            {{ Form::password('password_confirmation',['class' => 'md-input']) }}
            <label>Confirm Password</label>
        </div>

        <button type="submit" v-on:click="disableOnClick" class="btn primary btn-block p-x-md">Reset Password</button>

        {{ Form::close() }}
    </div>

@endsection
