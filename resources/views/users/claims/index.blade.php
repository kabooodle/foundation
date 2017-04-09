@extends('users.profile')

@section('profile-body')
    <div class="box white m-b-0">
        <div class="box-header">
            <h4>Claims</h4>
        </div>
    </div>
    <claims-list
        fetch_endpoint="{{ apiRoute('users.claims.index', [$viewedUser->username]) }}"
    ></claims-list>
@endsection
