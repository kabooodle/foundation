<p>Your claimed item, {!! $claim->listable->getTitle() !!}, for ${{ $claim->price }}, was rejected by the seller.</p>
{{ nl2br(e($claim->rejected_reason)) ? : 'No reason given'  }}
<p>You can view details of the item and claim here: <a href="{{ route('profile.claims.show', [$claim->getUUID()]) }}">{{ route('profile.claims.show', [$claim->getUUID()]) }}</a></p>