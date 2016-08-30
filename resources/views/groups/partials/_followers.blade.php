@if($item->followers->count() > 0)
    <ul class="list no-border p-b">
        @foreach($item->followers as $follower)
            <li class="list-item">
                <a href="{{ route('shop.show', [$follower->username] ) }}" class="list-left">
                        <span class="w-40 avatar">
                          <img src="https://placekitten.com/g/32/32" alt="...">
                            {{--<i class="on b-white bottom"></i>--}}
                        </span>
                </a>
                <div class="list-body">
                    <div class="m-t-sm text-ellipsis"><a href="{{ route('shop.show', [$follower->username] ) }}">{!! $follower->name !!}</a></div>
                </div>
            </li>
        @endforeach
    </ul>
@endif