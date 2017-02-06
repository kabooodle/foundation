
<div class="box white">
    <div class="row-col m-b">
        <div class="col-md-6">
            <div class="box no-shadow white p-a">
                <div id="item-{{$item->id}}-carousel" class="carousel image-carousel-container slide" data-ride="carousel">
                    <div class="carousel-outer">
                        <div class="carousel-inner" role="listbox">
                            @foreach($item->images as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : null }}" >
                                    <img
                                            data-toggle="lightbox"
                                            data-remote="{{ $image->location }}"
                                            data-gallery="gallery"
                                            src="{{ $image->location }}"
                                    >
                                </div>
                            @endforeach
                        </div>
                        <a class="left carousel-control" href="#item-{{$item->id}}-carousel" role="button" data-slide="prev">
                            <span class="icon-prev" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#item-{{$item->id}}-carousel" role="button" data-slide="next">
                            <span class="icon-next" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <ol class="carousel-indicators">
                        @foreach($item->images as $key=>$image)
                            <li data-target="#item-{{$item->id}}-carousel" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : null }}">
                                <img src="{{ $image->location }}" style="width: 64px; height: 64px;">
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box-header no-shadow">
                <h2><span class="_800">{!! $item->name !!}</span></h2>
                {{--<p class="block m-t-0"><span class="text-muted">Size:</span> {!! $item->styleSize->name !!}</p>--}}
                <p class="m-b-0 m-t-1 h4 text-warning _500">${{ isset($_price) ? $_price : $item->getPrice() }}</p>

                <div class="m-t-2 m-b-0">
                    <v-card
                            extra_class="list-group-item no-border b-a-0 "
                            :already_following="{{ $item->user->is_following ? 1 : 0 }}"
                            follow_endpoint="{{ apiRoute('user.followers.store', [$item->owner->id]) }}"
                            able_type="{{ get_class($item->owner) }}"
                            able_id="{{ $item->user->id }}"
                            :user="{{ $item->user }}"
                            message_endpoint="{{ apiRoute('messenger.store') }}"
                    ></v-card>
                </div>
            </div>
            <div class="box-body">
                <p class="m-b-lg text-muted text">{!! nl2br($item->description) !!}</p>
            </div>
        </div>
        <div class="col-md-2 b-l no-shadow">

            <div class=" p-a-md">
                <h6 class="text-muted">Categories</h6>
                @if($item->categories->count() > 0)
                    @foreach($item->categories as $tag)
                        <span class="label">{{ $tag->name }}</span>
                    @endforeach
                @else
                    <small class="text-muted text-sm"><em>None</em></small>
                @endif
            </div>
        </div>
    </div>
</div>