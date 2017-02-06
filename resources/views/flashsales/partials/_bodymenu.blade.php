<div class="clearfix">
    <div class="pull-left">
        <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(0,100) }}<span class="_400 text-muted"> Views</span></span>
        <span class="inline btn-group-vertical _500 m-l-sm" style="margin-top: 5px;">{{ $item->adminsAndSellers()->count() }} <span class="_400 text-muted"> Sellers</span></span>
        <span class="inline btn-group-vertical _500 m-l-sm" style="margin-top: 5px;">{{ $item->listedItems->count() }} <span class="_400 text-muted"> Items</span></span>
    </div>
    <div class="pull-right">
        <a data-turbolinks="false" href="#about" class="btn-link btn-sm">Items</a>
        <a data-turbolinks="false" href="#about" class="btn-link btn-sm">About</a>
        {{--<a data-turbolinks="false" href="#policies" class="btn-link btn-sm">Policies</a>--}}
        <a data-turbolinks="false" href="#" class="btn btn-sm primary b-primary">Watch</a>
        @if(user())
        <span class="b-l m-l m-r"></span>
            <div class="btn-group dropdown prpl-hover dropdown-onhover ">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label"><i class="fa fa-cog" aria-hidden="true"></i></span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right text-left text-sm">
                    <a href="{{ route('flashsales.edit', [$item->getUUID()]) }}" class="dropdown-item">
                        Edit Sale
                    </a>
                    <a href="{{ route('flashsales.shop.edit', [$item->getUUID(), webUser()->username]) }}" class="dropdown-item">
                        Manage Your Sale Items
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>