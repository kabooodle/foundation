@extends('layouts.full')

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
                            <a href="" class="btn btn-icon btn-social rounded white btn-sm">
                                <i class="fa fa-facebook"></i>
                                <i class="fa fa-facebook indigo"></i>
                            </a>
                            <a href="" class="btn btn-icon btn-social rounded white btn-sm">
                                <i class="fa fa-twitter"></i>
                                <i class="fa fa-twitter light-blue"></i>
                            </a>
                            <a href="" class="btn btn-icon btn-social rounded white btn-sm">
                                <i class="fa fa-google-plus"></i>
                                <i class="fa fa-google-plus red"></i>
                            </a>
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
                    {{--<div class="p-a">--}}
                    {{--<a href class="text-ellipsis"><i class="fa fa-link text-muted m-r-sm"></i> apack.com/subdomain</a>--}}
                    {{--<a href class="text-ellipsis"><i class="fa fa-globe text-muted m-r-sm"></i> yourdomain.com</a>--}}
                    {{--</div>--}}
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