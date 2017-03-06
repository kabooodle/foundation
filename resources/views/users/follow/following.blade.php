@extends('users.profile')

@section('profile-body')


    <div class="box">
        <div class="box-header b-b">
            <h3>Following</h3>
        </div>
        <div class="box-body">
            <following
                    no_results_text="{{ $viewedUser->username }} is not following anyone."
                    message_endpoint="{{ apiRoute('messenger.store') }}"
                    items_endpoint="{{ apiRoute('following.index', [$viewedUser->username]) }}"
            ></following>
        </div>
    </div>

@endsection
