@extends('layouts.full')

@section('body-content')


    <div class="p-l p-r">
        <div class="row">
        @yield('profile-body')

            <div class="col-sm-6 col-md-pull-6 col-md-3">
                <div class="box">
                    <div class="box-tool">
                        <ul class="nav">
                            <li class="nav-item inline dropdown">
                                <a class="nav-link text-muted" data-toggle="dropdown">
                                    <i class="material-icons md-18">&#xe164;</i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-scale pull-right dark">
                                    <a class="dropdown-item" href>Activities</a>
                                    <a class="dropdown-item" href>Feed</a>
                                    <a class="dropdown-item" href>Photo</a>
                                    <div class="dropdown-divider"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="p-a-md text-center">
                        <p><img src="https://unsplash.it/90/90/?random" class="img-circle w-xs"></p>
                        <a href class="text-md block">{{ $user->full_name }}</a>
                        <p><small>{{ $user->email }}</small></p>
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
                            <div id="profilePage">
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
                    </div>
                    <div class="row no-gutter b-t b-b">
                        <div class="col-xs-6 b-r">
                            <a href="{{ route('follow.followers', [$user->username]) }}" class="p-a block text-center">
                                <span class="block _600">{{ $user->followers->count() }}</span>
                                <span>Followers</span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <a href="{{ route('follow.following', [$user->username]) }}" class="p-a block text-center">
                                <span class="block _600">{{ $user->following->count() }}</span>
                                <span>Following</span>
                            </a>
                        </div>
                    </div>
                    <div class="p-a">
                        <a href class="text-ellipsis"><i class="fa fa-link text-muted m-r-sm"></i> apack.com/subdomain</a>
                        <a href class="text-ellipsis"><i class="fa fa-globe text-muted m-r-sm"></i> yourdomain.com</a>
                    </div>
                </div>
                <div class="box light lt">
                    <div class="box-body">
                        <a href class="pull-left m-r">
                            <img src="https://unsplash.it/32/32/?random" class="img-circle w-40">
                        </a>
                        <div class="clear p-a-xs">
                            <a href>@Mike Mcalidek</a>
                            <small class="block text-muted">2,415 followers / 225 tweets</small>
                            <a href class="btn btn-sm btn-rounded white m-t-xs"><i class="fa fa-twitter m-t-xs"></i> Follow</a>
                        </div>
                    </div>
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
<script src="/assets/js/profile.js"></script>
@endpush