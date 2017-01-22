<p>{{ $owner->username }} invited you to participate as a seller in the flashsale "{!! $flashsale->name !!}" which starts on {{ $flashsale->startsAtHuman() }} and ends {{ $flashsale->endsAtHuman() }}.</p>
<p>Your time slot for listing is {{ $flashsale->present()->timeslot($timeslot) }}.</p>
<p>The public link to view the items in the flashsale is here {{ route('flashsales.show', [$flashsale->uuid]) }}</p>

