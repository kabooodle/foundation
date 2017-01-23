
    <div class="box">
        <div class="item">

            <div class="item-overlay active p-a" style="z-index:999">
                @if($item->privacy == 'private')
                <a href="" class="pull-left text-u-c label label-md danger">{{ $item->privacy }}</a>
                @endif
                @if($item->saleHasEnded())
                        <span class="pull-left text-u-c label label-md info">Sale ended</span>
                @endif
            </div>

            <div class="item-overlay-bottom active p-a">
                <h2><a href="{{ route('flashsales.show', [$item->getUUID()]) }}">{!! $item->name !!} </a> @if(Request::is('groups/*/followers')) <span class="text-muted">- Followers</span> @elseif(Request::is('groups/*/members')) <span class="text-muted">- Members</span>  @endif</h2>
            </div>
            <div class="coverimage FlexEmbed FlexEmbed--4by1" style="background-image: url('{{ $item->coverimage->location }}')"></div>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body m-0 p-b-1">
            <div class="clearfix">
                <p class=" pull-left m-b-0">{{ $item->present()->getDateRange() }}</p>
                <p class="pull-right m-b-0">
                    <span class="">{{ $item->sellers()->count() }}</span> <span class="text-muted">Sellers</span>
                    <span class="m-l-sm ">{{ $item->listingItems->count() }}</span> <span class="text-muted">Items</span>
                </p>
                {{--<div class="pull-right">--}}
                    {{--@if(! $item->saleIsActive())--}}
                        {{--@if($item->userIsAdminOrSeller(userOrGuest()))--}}
                            {{--<p class="text-sm label  text-muted">Browsing as Seller</p>--}}
                        {{--@endif--}}
                    {{--@endif--}}
                {{--</div>--}}
            </div>
                    {{--<p class="text-muted text-left">Host: <a class="a" href="{{ route('groups.show', [$item->group->getUUID()]) }}">{!!  $item->group->name !!}</a></p>--}}
        </div>
    </div>

