<p>A claim of an item of yours on Kabooodle was canceled.</p>
<ul>
    <li><strong>Sale:</strong> {!! $item->listing->sale_name !!}</li>
    <li><strong>Sale Type:</strong> {!! ucfirst($item->listing->type) !!}</li>
    <li><strong>Claimed by:</strong> {!! $claim->claimer->username !!}</li>
    <li><strong>Item:</strong> {!! $claim->listable->getTitle() !!}</li>
    <li><strong>Price:</strong> {!! currency($claim->price) !!}</li>
</ul>
<p>View your other claims here: <a href="{{ route('shop.claims.index', [$seller->username]) }}">{{ route('shop.claims.index', [$seller->username]) }}</a>
</p>