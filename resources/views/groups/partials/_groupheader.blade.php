
<div class="padding">
    <div class="box">
        <div class="item">
            <div class="item-overlay active p-a">
                <a href="" class="pull-left text-u-c label label-md danger">{{ $item->privacy }} Group</a>
            </div>
            <div class="item-overlay-bottom active p-a">
                <h2><a href="{{ route('groups.show', [$item->getUUID()]) }}">{!! $item->name !!} </a> @if(Request::is('groups/*/followers')) <span class="text-muted">- Followers</span> @elseif(Request::is('groups/*/members')) <span class="text-muted">- Members</span>  @endif</h2>
            </div>
            <img src="https://placekitten.com/g/240/240" class="w-full" height="240">
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6 col-sm-push-6">
                    <div class="p-y-md pull-right">
                       <a href="" class="btn btn-sm default white">Leave Group</a>
                    </div>
                </div>
                <div class="col-sm-6 col-sm-pull-6">
                    <div class="p-y-md clearfix nav-active-primary">
                        <ul class="nav nav-pills nav-sm">
                            <li class="nav-item active" aria-expanded="false">
                                <a href="{{ route('groups.members.index', [$item->getUUID()]) }}" class="nav-link active" data-toggle="tab" data-target="#tab_1">Discussion</a>
                            </li>
                            <li class="nav-item" aria-expanded="false">
                                <a href="{{ route('groups.members.index', [$item->getUUID()]) }}" class="nav-link" data-toggle="tab" data-target="#tab_1">Members</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="" data-toggle="tab" data-target="#tab_2" aria-expanded="false">Flash Sales</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>