@extends('welcome')

@section('profile-body')

<div class="col-sm-12 col-md-push-3 col-md-6">
    <div class="box">
        <div class="box-header b-b">
            <h3>Following</h3>
        </div>
        <div class="box-body">
            <div class="row row-sm">
                @if($usersFollowing->count() > 0)
                    @foreach($usersFollowing as $user)
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
                    @else
                        @if(auth()->user() == $user)
                            <p>You aren't following anyone!</p>
                        @else
                            <p>{{ $user->full_name }} is not following anyone.</p>
                        @endif
                    @endif
            </div>
        </div>
    </div>
</div>
@endsection
