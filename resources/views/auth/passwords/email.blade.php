@extends('layouts.minimal')

@section('body-content')

@include('auth.partials.logo')


    <div class="p-a-md box-color r box-shadow-z1 text-color">

        {{ Form::open(['route' => ('auth.password.reset.send')]) }}

        <div class="md-form-group">
            {{ Form::text('email', null, ['class' => 'md-input']) }}
            <label>Email Address</label>
        </div>

        <button type="submit" v-on:click="disableOnClick" class="btn primary btn-block p-x-md">Send Password Reset Link</button>

        {{ Form::close() }}
    </div>



@endsection
