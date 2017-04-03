@extends('users.profile')

@section('profile-body')
    <div class="box white m-b-0">
        <div class="box-header">
            <h3>Claims</h3>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <claims-list
                    fetch_endpoint="{{ apiRoute('users.claims.index', [$viewedUser->username]) }}"
            ></claims-list>
        </div>
    </div>
@endsection
