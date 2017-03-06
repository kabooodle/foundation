@extends('users.profile')

@section('profile-body')


    <div class="box">
        <div class="box-header b-b">
            <h3>Followers</h3>
        </div>
        <div class="box-body">

                @if($followers->count() > 0)
                <div class="row row-sm">
                    @foreach($followers as $user)
                        <div class="col-sm-5">
                            <div class="p-a-md text-center">
                                <p><img src="https://unsplash.it/90/90/?random" class="img-circle w-xs"></p>
                                <a href class="text-md block">{{ $user->username }}</a>
                                <followable
                                    able_type="{{ get_class($user) }}"
                                    able_id="{{ $user->id }}"
                                    :already_following="{{ $user->is_followed ? 1 : 0 }}"
                                    endpoint="{{ apiRoute('user.followers.store', [$user->id]) }}"
                                    followable_entity_name="user"
                                    followable_type="user"
                                    followable_id="{{ $user->id }}">
                                </followable>
                            </div>
                        </div>
                    @endforeach
                </div>
                    @else
                        @if(webUser() && webUser()->id == $viewedUser->id)
                            <p>You have no followers! :(</p>
                        @else
                            <p>{{ $viewedUser->username }} does not have any followers</p>
                        @endif
                    @endif

        </div>
    </div>




@endsection
