@extends('layouts.full')

@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Followers</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
                <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                    <thead>
                    </thead>
                    <tbody>
                        @if($followers->count() > 0)
                            @foreach($followers as $follower)
                                <div class="col-sm-3">
                                <div class="p-a-md text-center">
                                    <p><img src="https://unsplash.it/90/90/?random" class="img-circle w-xs"></p>
                                    <a href class="text-md block">{{ $follower->user->full_name }}</a>
                                    <div id="follow">
                                            <followable
                                                    able_type="{{ get_class($follower->user) }}"
                                                    able_id="{{ $follower->user->id }}"
                                                    :already_following="{{ $follower->user->is_followed ? 1 : 0 }}"
                                                    endpoint="{{ apiRoute('user.followers.store', [$follower->user->id]) }}"
                                                    followable_entity_name="user"
                                                    followable_type="user"
                                                    followable_id="{{ $follower->user->id }}">
                                            </followable>
                                        </div>
                                    </div>
                                    </div>
                            @endforeach
                        @else
                            <p>You have no followers! :(</p>
                        @endif
                    </tbody>
                </table>


        </div>
    </div>

@endsection

@push('footer-scripts')
<script src="/assets/js/follow.js"></script>
@endpush