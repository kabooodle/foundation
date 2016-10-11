@extends('profile.settingstemplate')


@section('settings-content')

    {{ Form::open(['route' => 'profile.creditcard.store']) }}

    <div class="box">
        <div class="box-header">
            <h2>@if($card) Update Your @endif Credit  Card</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            @include('profile.creditcard.partials._form')
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>

    {{ Form::close() }}

@endsection

