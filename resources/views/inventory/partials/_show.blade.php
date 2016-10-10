<div class="box white">
    <div class="row-col m-b">
        <div class="col-md-6">
            <div class="box no-shadow white">
                <div class="p-t-1 p-l-1">
                    <img class="img-responsive"
                         data-toggle="lightbox"
                         data-gallery="gallery_{{ $item->id }}"
                         data-remote="{{ $item->firstImage() ? $item->firstImage()->location : 'http://s3-us-west-2.amazonaws.com/hypebeast-wordpress/image/2009/07/huf-converse-product-red-skidgrip-2.jpg' }}"
                         src="{{ $item->firstImage() ? $item->firstImage()->location : 'http://s3-us-west-2.amazonaws.com/hypebeast-wordpress/image/2009/07/huf-converse-product-red-skidgrip-2.jpg' }}">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box-header no-shadow">
                <h2><span class="_800">{!! $item->name !!}</span></h2>
                <p class="block m-t-0"><span class="text-muted">Size:</span> {!! $item->styleSize->name !!}</p>
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

            <div class="text-center">
                <small class="text-muted"><i class="fa fa-flag" aria-hidden="true"></i> Flag</small>
            </div>
        </div>
    </div>
</div>