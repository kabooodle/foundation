@extends('users.profile')

@section('profile-body')

    <div class="box white m-b-0">
        <div class="box-header">
            <h3>Listings</h3>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <listings-list
                    fetch_endpoint="{{ apiRoute('users.listings.index', [$viewedUser->username]) }}"
            ></listings-list>
        </div>
    </div>
@endsection
