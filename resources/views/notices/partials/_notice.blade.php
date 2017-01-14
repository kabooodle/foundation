<li class="list-group-item black lt box-shadow-z0 b">
    <span class="pull-left m-r">
        <img alt="..." src="../assets/images/a0.jpg" class="w-40 img-circle">
    </span>
    <span class="clear block">
        {{ $notice->title }}
        <br>
        <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
    </span>
</li>