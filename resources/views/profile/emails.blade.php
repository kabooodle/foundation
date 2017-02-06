@extends('profile.settingstemplate')

@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Email Addresses</h2>
            <small>Below are all the email addresses on file for you.</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            @include('profile.partials._emails', ['_user' => webUser(), '_emails' => $emails, '_primary_email' => $primaryEmail])
        </div>
    </div>

@endsection
