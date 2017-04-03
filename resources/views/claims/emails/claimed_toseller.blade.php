<p>An item of yours on Kabooodle was claimed!</p>
<ul>
    <li><strong>Sale:</strong> {!! $item->listing->sale_name !!}</li>
    <li><strong>Sale Type:</strong> {!! ucfirst($item->listing->type) !!}</li>
    <li><strong>Claimed by:</strong> {!! $claim->claimer->username !!}</li>
    <li><strong>Item:</strong> {!! $claim->listable->getTitle() !!}</li>
    <li><strong>Price:</strong> ${!! $claim->price !!}</li>
</ul>
<p>Please login and accept or reject this claim as soon as possible.  Accepting the claim will complete the transaction, and convert the claim to a sale.  Rejecting the claim returns the item
    to your inventory immediately. Pending claims url: <a href="{{ route('shop.claims.index', [$seller->username]) }}">{{ route('shop.claims.index', [$seller->username]) }}</a>
</p>