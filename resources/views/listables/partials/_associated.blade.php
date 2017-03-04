<a href="{{ $route }}">
    <div class="box inline m-r-sm">
            <span class="avatar_container _64 avatar-thumbnail">
                <img src="{{ $listable->coverphoto->location }}" >
            </span>
        <span class="p-a-o text-sm clearfix block text-center">
                {{ $listable->getTitle() }}
            </span>
    </div>
</a>
