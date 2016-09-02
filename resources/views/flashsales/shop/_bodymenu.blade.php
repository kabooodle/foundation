
<div class="clearfix">
    <div class="pull-left">
        <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Sales</span></span>
        <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Views</span></span>

    </div>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('shop.inventory.edit', [$inventory->user->username, $item->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
        <a href="" class="btn-sm btn white"><i class="fa fa-share" aria-hidden="true"></i> Share</a>
        <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x {{ $inventory->is_liked ? 'warning' : null }}"></i> {{ $inventory->likes->count() }} Likes</a>
    </div>
</div>