<div class="sl-item" data-id="{{ $_comment->uuid }}" data-author-id="{{ $_comment->author->public_hash }}" data-author="{{ $_comment->author->name }}">
    <div class="sl-left">
        <img src="https://unsplash.it/32/32/?random" class="img-circle">
    </div>
    <div class="sl-content">
        <div class="sl-author">
            <a href="" class="_600">{!! $_comment->author->name !!}</a>
        </div>
        <div>
            {!! nl2br($_comment->text)  !!}
        </div>
        <div class="sl-footer sl-date clearfix">
            <ul class="text-muted list-inline pull-left">
                <li class="list-inline-item"><time datetime="{{ $_comment->created_at }}">{{ $_comment->created_at->diffForHumans() }}</time></li>
                <li class="list-inline-item">Delete</li>
                <li class="list-inline-item"><a href="">Reply</a></li>
            </ul>
        </div>
    </div>
</div>