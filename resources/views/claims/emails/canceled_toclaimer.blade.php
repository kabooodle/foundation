<p>You canceled a claim on Kabooodle!</p>
<ul>
    <li><strong>Sale:</strong> {!! $item->listing->sale_name !!}</li>
    <li><strong>Sale Type:</strong> {!! ucfirst($item->listing->type) !!}</li>
    <li><strong>Seller:</strong> {!! $seller->username !!}</li>
    <li><strong>Item:</strong> {!! $claim->listable->getTitle() !!}</li>
    <li><strong>Price:</strong> {!! currency($claim->price) !!}</li>
</ul>
<p>You can view the status of the claim here: <a href="{{ route('profile.claims.show', [$claim->getUUID()]) }}">{{ route('profile.claims.show', [$claim->getUUID()]) }}</a></p>