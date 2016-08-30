@if($item->allMembers()->count() > 0)
    <div class="row">

        @foreach($item->allMembers() as $member)
            <div class="col-md-3">
                <ul class="list no-border p-b">
                    <li class="list-item">
                        <a href="{{ route('shop.show', [$member->username] ) }}" class="list-left">
                        <span class="w-40 avatar">
                          <img src="https://placekitten.com/g/32/32" alt="...">
                            {{--<i class="on b-white bottom"></i>--}}
                        </span>
                        </a>
                        <div class="list-body">
                            <div class="m-t-sm text-ellipsis"><a href="{{ route('shop.show', [$member->username] ) }}">{!! $member->name !!}</a></div>
                        </div>
                    </li>
                </ul>
            </div>
        @endforeach

    </div>
@endif