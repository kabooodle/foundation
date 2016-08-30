@extends('layouts.full')


@section('body-menu')

    <div class="clearfix">
        <div class="pull-left">
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Sales</span></span>
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Views</span></span>

        </div>
        <div class="btn-toolbar pull-right">
                <a href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
                <a href="" class="btn-sm btn white"><i class="fa fa-share" aria-hidden="true"></i> Share</a>
            <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x {{ $item->is_liked ? 'warning' : null }}"></i> {{ $item->likes->count() }} Likes</a>
            @if ($item->user_id == Auth::id())
                <span class="b-l m-l m-r"></span>
                <a href="{{ route('shop.inventory.edit', [$item->user->username, $item->getUUID()]) }}" class="btn btn-sm default white"><i class="fa fa-cog" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>

@endsection

@section('body-content')

    <div class="box white">
    <div class="row-col m-b">
        <div class="col-md-6">
            <div class="box no-border no-shadow white">
                <img class="img-responsive" src="http://s3-us-west-2.amazonaws.com/hypebeast-wordpress/image/2009/07/huf-converse-product-red-skidgrip-2.jpg">
            </div>
        </div>
        <div class="col-md-4">
                <div class="box-header no-shadow"><h2 class="_700">{!! $item->name !!}</h2>
                    <p class="m-b-0 m-t-1 h4 text-warning _500">${{ $item->getPrice() }}</p>
                    <div class="list-item m-t-2 m-b-0 box">
                        <a href="{{ route('shop.show', [$item->user->username]) }}" class="list-left">
                        <span class="w-40 avatar"><img
                                    src="https://placekitten.com/g/32/32" alt="..."> <i
                                    class="on b-white bottom"></i></span></a>
                        <div class="list-body">
                            <small class="_500 text-ellipsis">{{ $item->user->username }}</small>
                            <small class="text-muted">{{ $item->user->followers->count() }} Followers</small>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    {{--<ul class="nav nav-tabs m-b-2 b-b">--}}
                        {{--<li class="nav-item b-b-1 b-b-white ">--}}
                            {{--<a class="nav-link active _500" href="#">Description</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                    <p class="m-b-lg text-muted text">{!! nl2br($item->description) !!}</p>
                </div>
        </div>
        <div class="col-md-2 b-l no-shadow">
            @if($item->tags->count() > 0)
                <div class=" p-a-md">
                    <h6 class="text-muted">Tags:</h6>
                    @foreach($item->tags as $tag)
                        <span class="label">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
                <div class="text-center">
                    <small class="text-muted"><i class="fa fa-flag" aria-hidden="true"></i> Flag</small>
                </div>
        </div>
    </div>
    </div>



@endsection