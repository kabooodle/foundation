
    <div class="box">
        <div class="item">
            @if($item->privacy == 'private')
            <div class="item-overlay active p-a">
                <a href="" class="pull-left text-u-c label label-md danger">{{ $item->privacy }}</a>
            </div>
            @endif
            <div class="item-overlay-bottom active p-a">
                <h2><a href="{{ route('flashsales.show', [$item->getUUID()]) }}">{!! $item->name !!} </a> @if(Request::is('groups/*/followers')) <span class="text-muted">- Followers</span> @elseif(Request::is('groups/*/members')) <span class="text-muted">- Members</span>  @endif</h2>
            </div>
            <div class="coverimage FlexEmbed FlexEmbed--4by1" style="background-image: url('{{ $item->coverimage->location }}')"></div>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body m-0 p-b-1">
            <div class="clearfix">
                <p class="h6 _500 pull-left">{{ $item->startsAtHuman() }} - {{ $item->endsAtHuman() }}</p>
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

