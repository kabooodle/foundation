@extends('layouts.full')

@section('body-content')


    <div class="p-l p-r" id="profilePage">
        <div class="row">
        @yield('profile-body')

            <div class="col-sm-6 col-md-pull-6 col-md-3">
                <div class="box">
                    <div class="p-a-md text-center">
                        <span class="avatar_container _96 inline avatar-thumbnail">
                            <img alt="..." src="{{ webUser()->avatar->location}}">
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

            <div class="col-sm-6 col-md-3">
                <div class="box">
                    <div class="box-header">
                        <h4>Just followed you</h4>
                    </div>
                    <div class="list-group no-radius no-borders">
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm success"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Jonathan Morina</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm success"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Mason Yarnell</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm warning"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Mike Mcalidek</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm danger"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Cris Labiso</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Daniel Sandvid</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Helder Oliveira</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Jeff Broderik</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Daniel Sandvid</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Helder Oliveira</span>
                        </a>
                        <a data-toggle="modal" data-target="#chat" data-dismiss="modal"  class="list-group-item text-ellipsis">
                            <span class="w-8 rounded m-r-sm dker"></span>
                            <img src="https://unsplash.it/32/32/?random" class="w-24 m-r-sm img-circle">
                            <span>Jeff Broderik</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/profile.js') }}"></script>
@endpush