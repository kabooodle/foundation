@extends('profile.settingstemplate')

@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Email Addresses</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            @include('profile.partials._emails', ['_user' => user(), '_emails' => $emails])
        </div>
    </div>

@endsection
