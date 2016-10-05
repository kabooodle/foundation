
    <div class="box white">
        <div class="item padding">
            <a class="list-left " href="{{ route('flashsales.show', [$item->getUUID()]) }}">
                <span class="w-70 w-40 avatar">
                    <img src="https://placekitten.com/g/80/80" >
                </span>
            </a>
            <h6 class="_700"><a href="{{ route('flashsales.show', [$item->getUUID()]) }}">{{ $item->name }}</a></h6>
            <p class="text-muted p-b-0 m-b-0">Flash Sale dates: {{ $item->startsAtHuman() }} - {{ $item->endsAtHuman() }}</p>
         </div>
    </div>