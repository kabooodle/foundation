<p>{{ $owner->username }} invited you to participate as an admin in the flashsale "{!! $flashsale->name !!}" which starts on {{ $flashsale->startsAtHuman() }} and ends {{ $flashsale->endsAtHuman() }}.</p>
<p>As an admin, you are able to list your inventory anytime!</p>
<p>The public link to view the items in the flashsale is here {{ route('flashsales.show', [$flashsale->uuid]) }}</p>

