@extends('users.profile')

@section('profile-body')

    <div class="box white m-b-0">
        <div class="box-header">
            <h3>Listings</h3>
        </div>
    </div>
    <listings-list
            fetch_endpoint="{{ apiRoute('users.listings.index', [$viewedUser->username]) }}"
    ></listings-list>
@endsection
