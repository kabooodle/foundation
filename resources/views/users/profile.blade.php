@extends('layouts.full')

@push('facebook-tags')
<meta property="og:image" content="{{ $viewedUser->avatar->location }}" />
<meta property="og:title" content="{{ $viewedUser->username }} Kabooodle profile" />
@endpush


@section('body-content')

    <div class="p-l p-r" id="profilePage">
        <div class="row">
            <div class="col-md-3">
                <div class="box">
                    <div class="p-a-md text-center">
                        <span class="avatar_container _96 inline avatar-thumbnail">
                            <img alt="..." src="{{ $viewedUser->avatar->location}}">
                        </span>
                        <a href="{{ route('user.profile', [$viewedUser->username]) }}" class="m-b-sm text-md block">
                            {{ $viewedUser->username }}
                        </a>
                        {{--<p><small>{{ $viewedUser->email }}</small></p>--}}
                        <div>
                            @if($viewedUser->facebook_user_id)
                                <a href="http://www.facebook.com/{{ $viewedUser->facebook_user_id }}" class="btn btn-icon btn-social rounded white btn-sm">
                                    <i class="fa fa-facebook"></i>
                                    <i class="fa fa-facebook indigo"></i>
                                </a>
                            @endif
                            @if($viewedUser->social_twitter)
                                <a href="{{ $viewedUser->social_twitter }}" class="btn btn-icon btn-social rounded white btn-sm">
                                    <i class="fa fa-twitter"></i>
                                    <i class="fa fa-twitter light-blue"></i>
                                </a>
                            @endif
                            @if($viewedUser->social_instagram)
                                <a href="{{ $viewedUser->social_instagram}}" class="btn btn-icon btn-social rounded white btn-sm">
                                    <i class="fa fa-instagram"></i>
                                    <i class="fa fa-instagram pink"></i>
                                </a>
                            @endif
                            @if($viewedUser->social_youtube)
                                <a href="{{ $viewedUser->social_youtube }}" class="btn btn-icon btn-social rounded white btn-sm">
                                    <i class="fa fa-youtube"></i>
                                    <i class="fa fa-youtube red"></i>
                                </a>
                            @endif
                                @if($viewedUser->social_website)
                                    <a href="{{ $viewedUser->social_website }}" class="btn btn-icon btn-social rounded white btn-sm">
                                        <i class="fa fa-globe"></i>
                                        <i class="fa fa-globe green"></i>
                                    </a>
                                @endif
                        </div>
                        <div class="text-center m-t">
                            <followable
                                    able_type="{{ get_class($viewedUser) }}"
                                    able_id="{{ $viewedUser->id }}"
                                    :already_following="{{ $viewedUser->is_followed ? 1 : 0 }}"
                                    endpoint="{{ apiRoute('user.followers.store', [$viewedUser->id]) }}"
                                    followable_entity_name="user"
                                    followable_type="user"
                                    followable_id="{{ $viewedUser->id }}">
                            </followable>
                            <message-user
                                    recipient_name="{{ $viewedUser->username }}"
                                    recipient_id="{{ $viewedUser->id }}"
                                    endpoint="{{ apiRoute('messenger.store') }}"
                            ></message-user>
                        </div>
                    </div>
                    <div class="row no-gutter b-t">
                        <div class="col-xs-6 b-r">
                            <a href="{{ route('follow.followers', [$viewedUser->username]) }}" class="p-a block text-center">
                                <span class="block _600">{{ $viewedUser->followers->count() }}</span>
                                <span>Followers</span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <a href="{{ route('follow.following', [$viewedUser->username]) }}" class="p-a block text-center">
                                <span class="block _600">{{ $viewedUser->usersFollowing->count() }}</span>
                                <span>Following</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                @yield('profile-body')
            </div>
        </div>
    </div>

@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/profile.js') }}"></script>
@endpush
