@extends('users.profile')

@section('profile-body')

    <div class="box">
        <div class="box-header b-b">
            <h3>Followers</h3>
        </div>
        <div class="box-body">
            <followers
                    no_results_text="{{ $viewedUser->username }} has no followers."
                    message_endpoint="{{ apiRoute('messenger.store') }}"
                    items_endpoint="{{ apiRoute('followers.index', [$viewedUser->username]) }}"
            ></followers>
        </div>
    </div>

@endsection
