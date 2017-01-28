@extends('profile.settingstemplate')


@section('settings-content')

    {{ Form::open(['route' => 'profile.index', 'method' => 'POST']) }}
    <div class="box">
        <div class="box-header">
            <h2>Profile Settings</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            @include('profile.partials._profilesettings', ['_user' => $user, '_timezone' => $timezone])
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>

    {{ Form::close() }}

@endsection