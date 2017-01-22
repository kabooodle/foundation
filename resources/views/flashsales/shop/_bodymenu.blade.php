
<div class="clearfix">
    <div class="pull-left">
        <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,50) }} <span class="text-muted">Sales</span></span>
        <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ isset($shoppable) ? $shoppable->totalViews() : rand(0,10) }} <span class="text-muted">Views</span></span>
    </div>
    <div class="btn-toolbar pull-right">
        @if(! $inventory->canSatisfyRequestedQuantityOf(1))
            <div class="inline" data-toggle="tooltip" data-placement="bottom" title="Watch the item to be notified of availability">
            <a class="btn btn-sm claim  _800 disabled" disabled href="#">Out of stock!</a>
            </div>
        @else
        <a data-toggle="modal" data-target="#modal_claim_wrapper" data-backdrop="static" data-keyboard="false" href="{{ route('shop.inventory.edit', [$inventory->user->username, $item->getUUID()]) }}" class="btn btn-sm claim  _800 ">Claim it now!</a>
        @endif
        <a href="" class="btn-sm btn white"><i class="fa fa-share" aria-hidden="true"></i> Share</a>
        <a href="" class="btn-sm btn white"><i class="fa fa-heart-o fa-1x {{ $inventory->is_liked ? 'warning' : null }}"></i> {{ $inventory->likes->count() }} Likes</a>
    </div>
</div>