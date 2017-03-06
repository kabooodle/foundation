@foreach($categories as $categoryName => $sizes)
    <li
            data-style-id="{{ $styleId = $sizes->first()->first()->style_id }}"
            class="b-b style-list-item">
        <a  @click="toggleStyleListItem('{{ $styleId }}', $event)">
        @if(key($sizes->toArray()) <> '')
            <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
        @endif
        <i class="nav-label"><b class="label b label-sm no-bg text-muted ">{{ $sizes->flatten()->count() }}</b></i>
        <span class="nav-text">{{ $categoryName }}</span>
        </a>
        @if($sizes->count() > 0 && key($sizes->toArray()) <> '')
            <ul class="nav-sub">
                @foreach($sizes as $sizeName => $size)
                    <li class="size-list-item"
                        data-style-id="{{ $styleId }}"
                        data-size-id="{{ $size->first()->size_id }}"
                    >
                        <i class="nav-label m-r-1 m-l-0 "><b class="label label-sm no-bg text-muted ">{{ $size->flatten()->count() }}</b></i>
                        <a @click="toggleSizeListItem({{ $size->first()->size_id }}, {{ $styleId }}, $event)" ><span class="nav-text">{{ $sizeName }}</span></a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
